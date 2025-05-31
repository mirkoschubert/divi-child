import { useState, useEffect } from '@wordpress/element'
import {
  Modal,
  Button,
  Spinner
} from '@wordpress/components'
import { __ } from '@wordpress/i18n'

import { FormFieldRenderer } from './FormFieldRenderer'
import type { ModuleInfo, FieldConfig } from '@/types'

import './SettingsModal.styl'

interface SettingsModalProps {
  moduleSlug: string
  module: ModuleInfo
  onClose: () => void
  onSave: (settings: Record<string, unknown>) => Promise<void>
}

const SettingsModal: React.FC<SettingsModalProps> = ({
  moduleSlug,
  module,
  onClose,
  onSave
}) => {
  const [formData, setFormData] = useState<Record<string, unknown>>({})
  const [isLoading, setIsLoading] = useState(false)
  const [isSaving, setIsSaving] = useState(false)
  const [hiddenFields, setHiddenFields] = useState<Set<string>>(new Set())

  // Initialisiere Formulardaten
  useEffect(() => {
    const initialData: Record<string, unknown> = {}

    Object.entries(module.admin_settings).forEach(([fieldId, fieldConfig]) => {
      if (fieldId === 'enabled') return // Skip enabled field

      const currentValue = module.options[fieldId]
      initialData[fieldId] =
        currentValue !== undefined ? currentValue : fieldConfig.default
    })

    setFormData(initialData)
    updateFieldVisibility(initialData)
  }, [module])

  // Update field visibility based on dependencies
  const updateFieldVisibility = (data: Record<string, unknown>) => {
    const hidden = new Set<string>()

    Object.entries(module.admin_settings).forEach(([fieldId, fieldConfig]) => {
      if (fieldConfig.depends_on && fieldConfig.depends_value !== undefined) {
        const dependentValue = data[fieldConfig.depends_on]
        const requiredValue = fieldConfig.depends_value

        if (dependentValue !== requiredValue) {
          hidden.add(fieldId)
        }
      }
    })

    setHiddenFields(hidden)
  }

  const handleFieldChange = (fieldId: string, value: unknown) => {
    const newData = { ...formData, [fieldId]: value }
    setFormData(newData)
    updateFieldVisibility(newData)
  }

  const handleToggleChange = (fieldId: string, isChecked: boolean) => {
    handleFieldChange(fieldId, isChecked)
  }

  const handleSave = async () => {
    setIsSaving(true)
    try {
      await onSave(formData)
    } catch (error) {
      console.error('Error saving settings:', error)
    } finally {
      setIsSaving(false)
    }
  }

  const renderField = (fieldId: string, fieldConfig: FieldConfig) => {
    if (fieldId === 'enabled' || hiddenFields.has(fieldId)) {
      return null
    }

    return (
      <FormFieldRenderer
        key={fieldId}
        fieldId={fieldId}
        fieldConfig={fieldConfig}
        value={formData[fieldId]}
        onChange={(value) => handleFieldChange(fieldId, value)}
        onToggle={handleToggleChange}
      />
    )
  }

  return (
    <Modal
      title={`${module.name} ${__('Settings', 'divi-child')}`}
      onRequestClose={onClose}
      className="dvc-settings-modal"
      size="medium"
      style={{ 
        maxWidth: '600px',
        width: '90vw',
        height: 'auto',
        maxHeight: '90vh'
      }}
    >
      {isLoading ? (
        <div className="dvc-loading">
          <Spinner />
          <p>{__('Loading settings...', 'divi-child')}</p>
        </div>
      ) : (
        <>
          <div className="dvc-form">
            {Object.entries(module.admin_settings).map(([fieldId, fieldConfig]) =>
              renderField(fieldId, fieldConfig)
            )}
          </div>

          <div className="dvc-modal-footer">
            <Button
              variant="tertiary"
              onClick={onClose}
              disabled={isSaving}
            >
              {__('Cancel', 'divi-child')}
            </Button>

            <Button
              variant="primary"
              onClick={handleSave}
              isBusy={isSaving}
              disabled={isSaving}
            >
              {isSaving
                ? __('Saving...', 'divi-child')
                : __('Save Settings', 'divi-child')
              }
            </Button>
          </div>
        </>
      )}
    </Modal>
  )
}

export default SettingsModal
