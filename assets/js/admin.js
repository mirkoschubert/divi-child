/**
 * Divi Child Theme Admin JS
 * Eine modulare Implementierung der Admin-UI-Funktionalität
 */

class DiviChildAdmin {
  /**
   * Initialisiert die Admin-Klasse
   */
  constructor() {
    this.$ = jQuery;
    this.ajax = dvc_ajax;
    
    // Untermodule initialisieren
    this.moduleManager = new ModuleManager(this);
    this.modalManager = new ModalManager(this);
    this.validator = new FormValidator(this);
    this.listManager = new ListManager(this);
    
    // Event-Listener registrieren
    this.registerEventListeners();
  }
  
  /**
   * Registriert globale Event-Listener
   */
  registerEventListeners() {
    // Modal-Events
    this.$('.settings-btn').on('click', e => this.modalManager.openModal(e));
    this.$(document).on('click', '.dvc-modal-close, .close-modal-btn', () => this.modalManager.closeModal());
    
    // Speichern-Button
    this.$(document).on('click', '.save-module-settings', e => this.modalManager.saveSettings(e));
    
    // Liste-Events
    this.$(document).on('click', '.add-list-item', e => this.listManager.addListItem(e));
    this.$(document).on('click', '.remove-list-item', e => this.listManager.removeListItem(e));
    this.$(document).on('keypress', '.list-entry-input', e => this.listManager.handleKeyPress(e));
    this.$(document).on('input', '.list-entry-input', e => this.validator.validateListInput(e.target));
  }
}

/**
 * Verwaltet die Modul-Toggles und Modul-Status-Updates
 */
class ModuleManager {
  constructor(admin) {
    this.admin = admin;
    this.$ = admin.$;
    
    // Modul-Toggle aktivieren
    this.$('.module-toggle').on('change', e => this.toggleModule(e));
  }
  
  /**
   * Ändert den visuellen Status eines Moduls
   */
  toggleStatus($module, isEnabled) {
    const $settingsButton = $module.find('button.settings-btn');

    $module.addClass(isEnabled ? 'enabled' : 'disabled');
    $module.removeClass(isEnabled ? 'disabled' : 'enabled');
    $settingsButton.prop('disabled', !isEnabled);
  }
  
  /**
   * Verarbeitet das Umschalten eines Moduls und aktualisiert den Status via AJAX
   */
  toggleModule(event) {
    const $toggle = this.$(event.currentTarget);
    const moduleSlug = $toggle.data('slug');
    const isEnabled = $toggle.prop('checked');
    const $module = $toggle.closest('.module');
    
    // Visuelles Feedback während der AJAX-Anfrage
    $module.addClass('saving');
    
    // AJAX-Anfrage
    this.$.ajax({
      url: this.admin.ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'dvc_toggle_module',
        nonce: this.admin.ajax.nonce,
        slug: moduleSlug,
        enabled: isEnabled
      },
      success: response => {
        $module.removeClass('saving');
        
        if (response.success) {
          this.toggleStatus($module, isEnabled);
          $module.addClass('success');
          console.log(response.data.message);
          
          setTimeout(() => {
            $module.removeClass('success');
          }, 1500);
        } else {
          $module.addClass('error');
          console.error(response.data.message);
          
          setTimeout(() => {
            $module.removeClass('error');
          }, 1500);
        }
      },
      error: (xhr, status, error) => {
        console.error('AJAX-Fehler:', status, error);
        $module.removeClass('saving').addClass('error');
        setTimeout(() => {
          $module.removeClass('error');
        }, 1500);
      }
    });
  }
}

/**
 * Verwaltet den Modal-Dialog für Moduleinsteilungen
 */
class ModalManager {
  constructor(admin) {
    this.admin = admin;
    this.$ = admin.$;
    this.currentModuleSlug = null;
  }
  
  /**
   * Öffnet das Modal und lädt die Moduleinsteilungen
   */
  openModal(event) {
    const $button = this.$(event.currentTarget);
    const moduleSlug = $button.data('slug');
    this.currentModuleSlug = moduleSlug;
    
    const $modal = this.$('#module-settings-modal');
    const $container = this.$('#module-settings-container');
    
    // Zurücksetzen des Status-Bereichs beim Öffnen des Modals
    const $status = this.$('#save-status');
    $status.attr('class', 'save-status').text('');
    
    $container.html('<div class="loading-spinner"></div>');
    $modal.show();
    
    this.loadModuleSettings(moduleSlug);
  }
  
  /**
   * Schließt das Modal
   */
  closeModal() {
    this.$('#module-settings-modal').hide();
    this.currentModuleSlug = null;
  }
  
  /**
   * Lädt die Moduleinsteilungen via AJAX
   */
  loadModuleSettings(moduleSlug) {
    const $container = this.$('#module-settings-container');
    
    this.$.ajax({
      url: this.admin.ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'dvc_load_module_settings',
        nonce: this.admin.ajax.nonce,
        module: moduleSlug
      },
      success: response => {
        if (response.success) {
          this.$('#module-settings-title').text(response.data.title);
          $container.html(response.data.form);
          
          // Validierungsmeldungen global speichern
          window.dvcValidationMessages = response.data.validation_messages || {};
          
          // Verarbeitung von Feld-Abhängigkeiten
          this.initDependencies();
        } else {
          $container.html(`<p class="error-message">${response.data.message}</p>`);
        }
      },
      error: () => {
        $container.html('<p class="error-message">Fehler beim Laden der Einstellungen.</p>');
      }
    });
  }
  
  /**
   * Speichert alle Einstellungen im Modal
   */
  saveSettings() {
    const $form = this.$('#module-settings-form');
    const moduleSlug = $form.data('module');
    
    if (!moduleSlug) {
      console.error('Kein Modul-Slug gefunden');
      return;
    }
    
    // Status anzeigen
    const $status = this.$('#save-status');
    $status.attr('class', 'save-status saving').text(this.admin.ajax.messages.saving);
    
    // Formularwerte sammeln
    const formData = this.collectFormData($form);
    
    // AJAX-Anfrage zum Speichern
    this.$.ajax({
      url: this.admin.ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'dvc_save_module_settings',
        nonce: this.admin.ajax.nonce,
        slug: moduleSlug,
        settings: formData
      },
      success: response => {
        if (response.success) {
          $status.attr('class', 'save-status success')
                 .text(this.admin.ajax.messages.success);
          
          // Nach erfolgreichem Speichern kurz die Erfolgsmeldung anzeigen und dann schließen
          setTimeout(() => {
            this.closeModal();
          }, 1000);
        } else {
          $status.attr('class', 'save-status error')
                 .text(response.data.message || this.admin.ajax.messages.error);
        }
      },
      error: (xhr, status, error) => {
        console.error('AJAX-Fehler beim Speichern:', status, error);
        $status.attr('class', 'save-status error')
               .text(this.admin.ajax.messages.error);
      }
    });
  }
  
  /**
   * Sammelt alle Formulardaten und bereitet sie für AJAX auf
   */
  collectFormData($form) {
    const formData = {};
    
    $form.find('input, select, textarea').each((i, field) => {
      const $field = this.$(field);
      const name = $field.attr('name');
      
      // Überspringe Elemente ohne Namen
      if (!name) return;
      
      // Array-Felder (name="field_name[]") speziell behandeln
      if (name.endsWith('[]')) {
        const baseName = name.slice(0, -2);
        if (!formData[baseName]) {
          formData[baseName] = [];
        }
        formData[baseName].push($field.val());
        return;
      }
      
      // Reguläre Felder
      if ($field.attr('type') === 'checkbox') {
        formData[name] = $field.is(':checked');
      } else {
        formData[name] = $field.val();
      }
    });
    
    return formData;
  }
  
  /**
   * Verarbeitung von Feld-Abhängigkeiten
   */
  initDependencies() {
    // Wichtig: Zuerst alle bestehenden Event-Handler entfernen, um Mehrfachbindungen zu vermeiden
    this.$('form').off('change', '.toggle-input');
    
    // Dann neuen Event-Handler hinzufügen
    this.$('form').on('change', '.toggle-input', (event) => {
      const $toggle = this.$(event.currentTarget);
      const fieldId = $toggle.attr('id');
      const isChecked = $toggle.prop('checked');
      
      this.updateDependentFields(fieldId, isChecked);
    });
    
    // Initial alle abhängigen Felder basierend auf dem aktuellen Status der Toggles aktualisieren
    this.$('form .toggle-input').each((index, toggle) => {
      const $toggle = this.$(toggle);
      const fieldId = $toggle.attr('id');
      const isChecked = $toggle.prop('checked');
      
      this.updateDependentFields(fieldId, isChecked);
    });
  }
  
  /**
   * Aktualisiert alle von einem Toggle abhängigen Felder
   */
  updateDependentFields(fieldId, isChecked) {
    // Suche nach abhängigen Feldern (jetzt direkt .dvc-field mit data-depends-on)
    this.$(`.dvc-field[data-depends-on="${fieldId}"]`).each((index, element) => {
      const $field = this.$(element);
      const requiredValueStr = $field.attr('data-depends-value');
      
      // Einfache, direkte Logik basierend auf dem erforderlichen Wert
      if (requiredValueStr === 'true') {
        // Wenn true verlangt wird, zeige es nur wenn checked ist
        $field.toggleClass('hidden', !isChecked);
      } 
      else if (requiredValueStr === 'false') {
        // Wenn false verlangt wird, zeige es nur wenn nicht checked ist
        $field.toggleClass('hidden', isChecked);
      }
    });
  }
}

/**
 * Verwaltet Listen-Elemente und deren Manipulation
 */
class ListManager {
  constructor(admin) {
    this.admin = admin;
    this.$ = admin.$;
  }
  
  /**
   * Fügt ein neues Element zu einer Liste hinzu
   */
  addListItem(event) {
    event.preventDefault();
    
    const $button = this.$(event.currentTarget);
    const targetId = $button.data('target');
    const $input = this.$(`#${targetId}_new`);
    const $container = this.$(`#${targetId}_items`);
    const value = $input.val().trim();
    
    if (!value) return;
    
    // Validierung
    const pattern = $input.attr('pattern');
    if (pattern && !new RegExp(pattern).test(value)) {
      const errorMessage = this.getValidationMessage(targetId);
      this.admin.validator.showValidationError($input, errorMessage);
      return;
    }
    
    // Validierung bestanden
    this.admin.validator.clearValidationError($input);
    
    // Neues Element erstellen
    const newItem = this.createListItem(targetId, value);
    $container.append(newItem);
    $input.val('').focus();
    
    // Leere Nachricht ausblenden
    $container.closest('.list-field').find('.list-empty-message').hide();
  }
  
  /**
   * Entfernt ein Element aus einer Liste
   */
  removeListItem(event) {
    event.preventDefault();
    
    const $item = this.$(event.currentTarget).closest('.list-item');
    const $container = $item.parent();
    const $field = $container.closest('.list-field');
    
    $item.remove();
    
    // Leere Nachricht anzeigen, wenn keine Einträge mehr vorhanden
    if ($container.children().length === 0) {
      $field.find('.list-empty-message').show();
    }
  }
  
  /**
   * Behandelt Tastendruck-Events in Listen-Eingabefeldern
   */
  handleKeyPress(event) {
    if (event.which === 13) {
      event.preventDefault();
      this.$(event.currentTarget).closest('.list-input-wrapper')
          .find('.add-list-item').click();
    }
  }
  
  /**
   * Erstellt das HTML für ein neues Listenelement
   */
  createListItem(targetId, value) {
    const escapedValue = this.escapeHtml(value);
    
    return this.$(`
      <li class="list-item">
        <span class="list-item-text">${escapedValue}</span>
        <input type="hidden" name="${targetId}[]" value="${escapedValue}">
        <button type="button" class="button button-small remove-list-item" aria-label="Eintrag entfernen">
          <span class="dashicons dashicons-no-alt"></span>
        </button>
      </li>
    `);
  }
  
  /**
   * Holt die Validierungsmeldung für ein Feld
   */
  getValidationMessage(fieldId) {
    return window.dvcValidationMessages && window.dvcValidationMessages[fieldId]
      ? window.dvcValidationMessages[fieldId]
      : 'Ungültiger Wert';
  }
  
  /**
   * Escape HTML-Sonderzeichen
   */
  escapeHtml(str) {
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
}

/**
 * Verwaltet die Formularvalidierung
 */
class FormValidator {
  constructor(admin) {
    this.admin = admin;
    this.$ = admin.$;
  }
  
  /**
   * Validiert eine Listeneingabe gegen ein Pattern
   */
  validateListInput(input) {
    const $input = this.$(input);
    const pattern = $input.attr('pattern');
    const value = $input.val().trim();
    const targetId = $input.attr('id').replace('_new', '');
    
    if (pattern && value) {
      if (!new RegExp(pattern).test(value)) {
        const errorMessage = window.dvcValidationMessages && window.dvcValidationMessages[targetId]
          ? window.dvcValidationMessages[targetId]
          : 'Ungültiger Wert';
        
        this.showValidationError($input, errorMessage);
      } else {
        this.clearValidationError($input);
      }
    } else {
      this.clearValidationError($input);
    }
  }
  
  /**
   * Zeigt eine Validierungsfehlermeldung an
   */
  showValidationError($input, message) {
    const $validationMessage = $input.closest('.list-field').find('.validation-message');
    $validationMessage.addClass('error').text(message);
    $input.addClass('has-error');
  }
  
  /**
   * Entfernt eine Validierungsfehlermeldung
   */
  clearValidationError($input) {
    const $validationMessage = $input.closest('.list-field').find('.validation-message');
    $validationMessage.removeClass('error').text('');
    $input.removeClass('has-error');
  }
}

// Beim DOM-Ready die Admin-Klasse initialisieren
jQuery(document).ready(function() {
  window.diviChildAdmin = new DiviChildAdmin();
});
