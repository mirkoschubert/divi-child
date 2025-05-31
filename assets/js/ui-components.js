/**
 * UI-Komponenten für das Admin-Interface
 */

/**
 * Verwaltet Repeater-Fields
 */
class RepeaterManager {
  constructor(admin) {
    this.admin = admin;
    this.$ = admin.$;
    
    // Event-Listener registrieren
    this.$(document).on('click', '.add-repeater-item', e => this.addRepeaterItem(e));
    this.$(document).on('click', '.remove-repeater-item', e => this.removeRepeaterItem(e));
  }
  
  /**
   * Fügt einen neuen Repeater-Eintrag hinzu
   */
  addRepeaterItem(event) {
    event.preventDefault();
    
    const $button = this.$(event.currentTarget);
    const targetId = $button.data('target');
    const $repeaterField = $button.closest('.repeater-field');
    const $inputArea = $repeaterField.find('.repeater-input-area');
    const $tableContainer = $repeaterField.find('.repeater-table-container');
    
    // Sammle Werte aus den Eingabefeldern
    const rowData = {};
    $inputArea.find('.repeater-input-field').each((i, fieldEl) => {
      const $field = this.$(fieldEl);
      const fieldName = $field.data('field');
      const $input = $field.find('input, select, textarea').first();
      
      rowData[fieldName] = $input.attr('type') === 'checkbox' ? $input.is(':checked') : $input.val();
    });
    
    // Validierung - leere Felder prüfen
    const hasEmptyFields = Object.values(rowData).some(value => 
      value === '' || value === null || value === undefined
    );
    
    if (hasEmptyFields) {
      alert('Bitte füllen Sie alle Felder aus.');
      return;
    }
    
    // Neue Tabellenzeile erstellen
    this.addTableRow(targetId, $tableContainer, rowData);
    
    // Eingabefelder zurücksetzen
    this.resetInputFields($inputArea);
    
    // Leere Nachricht ausblenden
    $repeaterField.find('.repeater-empty-message').hide();
  }
  
  /**
   * Setzt die Eingabefelder zurück
   */
  resetInputFields($inputArea) {
    $inputArea.find('input, select, textarea').each((i, el) => {
      const $el = this.$(el);
      const type = $el.attr('type');
      
      if (type === 'checkbox' || type === 'radio') {
        $el.prop('checked', false);
      } else {
        $el.val('');
      }
    });
  }
  
  /**
   * Entfernt einen Repeater-Eintrag
   */
  removeRepeaterItem(event) {
    event.preventDefault();
    
    const $row = this.$(event.currentTarget).closest('tr');
    const $table = $row.closest('table');
    const $repeaterField = $row.closest('.repeater-field');
    
    $row.remove();
    
    // Indizes neu nummerieren
    this.reindexTable($table);
    
    // Leere Nachricht anzeigen, wenn keine Zeilen mehr da sind
    if ($table.find('tbody tr').length === 0) {
      $repeaterField.find('.repeater-empty-message').show();
      $table.hide();
    }
  }
  
  /**
   * Nummeriert Tabellenzeilen neu
   */
  reindexTable($table) {
    const targetId = $table.closest('.repeater-field').find('.add-repeater-item').data('target');
    
    $table.find('tbody tr').each((index, row) => {
      const $row = this.$(row);
      $row.attr('data-index', index);
      
      // Alle Hidden Inputs neu nummerieren
      $row.find('input[type="hidden"]').each((i, input) => {
        const $input = this.$(input);
        const name = $input.attr('name');
        const fieldMatch = name.match(/\[(\w+)\]$/);
        
        if (fieldMatch) {
          const fieldName = fieldMatch[1];
          $input.attr('name', `${targetId}[${index}][${fieldName}]`);
        }
      });
    });
  }
  
  /**
   * Fügt eine neue Tabellenzeile hinzu
   */
  addTableRow(targetId, $tableContainer, rowData) {
    let $table = $tableContainer.find('table');
    
    // Tabelle erstellen, falls sie nicht existiert
    if ($table.length === 0) {
      $table = this.createTable(targetId, $tableContainer, rowData);
    }
    
    const $tbody = $table.find('tbody');
    const newIndex = $tbody.find('tr').length;
    
    // Neue Zeile erstellen
    const $newRow = this.$('<tr>').attr('data-index', newIndex);
    
    Object.entries(rowData).forEach(([fieldName, value]) => {
      const $cell = this.$('<td>');
      $cell.text(value);
      
      // Hidden Input für Formular-Submission
      const $hiddenInput = this.$('<input>')
        .attr('type', 'hidden')
        .attr('name', `${targetId}[${newIndex}][${fieldName}]`)
        .val(value);
      
      $cell.append($hiddenInput);
      $newRow.append($cell);
    });
    
    // Aktions-Spalte
    const $actionsCell = this.$('<td>');
    const $removeBtn = this.$('<button>')
      .attr('type', 'button')
      .addClass('button button-small remove-repeater-item')
      .html('<span class="dashicons dashicons-trash"></span>');
    
    $actionsCell.append($removeBtn);
    $newRow.append($actionsCell);
    
    $tbody.append($newRow);
    $table.show();
  }
  
  /**
   * Erstellt eine neue Tabelle
   */
  createTable(targetId, $container, sampleData) {
    const $table = this.$('<table>')
      .addClass('repeater-table wp-list-table widefat fixed striped');
    
    // Header erstellen
    const $thead = this.$('<thead>');
    const $headerRow = this.$('<tr>');
    
    Object.keys(sampleData).forEach(fieldName => {
      // Für bessere UX könnten wir hier die Labels aus der Konfiguration holen
      const label = fieldName.charAt(0).toUpperCase() + fieldName.slice(1);
      $headerRow.append(this.$('<th>').text(label));
    });
    
    $headerRow.append(this.$('<th>').attr('width', '80').text('Aktionen'));
    $thead.append($headerRow);
    
    const $tbody = this.$('<tbody>').attr('id', `${targetId}_table_body`);
    
    $table.append($thead, $tbody);
    $container.html($table);
    
    return $table;
  }
}

/**
 * Erweiterte UI-Funktionen
 */
class UIHelpers {
  constructor(admin) {
    this.admin = admin;
    this.$ = admin.$;
  }
  
  /**
   * Drag & Drop für Tabellen-Zeilen
   */
  initSortable() {
    this.$('.repeater-table tbody').sortable({
      handle: '.sort-handle',
      update: () => this.reindexAllTables()
    });
  }
  
  /**
   * Nummeriert alle Tabellen neu
   */
  reindexAllTables() {
    this.$('.repeater-table').each((i, table) => {
      const $table = this.$(table);
      const repeaterManager = this.admin.repeaterManager;
      repeaterManager.reindexTable($table);
    });
  }
  
  /**
   * Initialisiert erweiterte UI-Features
   */
  init() {
    this.initSortable();
  }
}