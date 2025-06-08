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
  const [expandedGroup, setExpandedGroup] = useState<string | null>(null)

  // Initialisiere Formulardaten
  useEffect(() => {
    const initialData: Record<string, unknown> = {}
    let firstGroupFound = false

    const flattenFields = (fields: Record<string, any>, prefix = '') => {
      Object.entries(fields).forEach(([fieldId, fieldConfig]) => {
        if (fieldId === 'enabled') return // Skip enabled field

        if (fieldConfig.type === 'group') {
          // Set first group as expanded
          if (!firstGroupFound) {
            setExpandedGroup(fieldId)
            firstGroupFound = true
          }
          // Recursively flatten group fields
          if (fieldConfig.fields) {
            flattenFields(fieldConfig.fields, prefix)
          }
        } else {
          // Regular field - use current options or default
          const currentValue = module.options[fieldId]
          initialData[fieldId] = currentValue !== undefined ? currentValue : fieldConfig.default
        }
      })
    }

    flattenFields(module.admin_settings)
    console.log('🔍 Initial form data:', initialData)
    setFormData(initialData)
  }, [module])

  const handleFieldChange = (fieldId: string, value: unknown) => {
    const newData = { ...formData, [fieldId]: value }
    setFormData(newData)
  }

  const handleToggleChange = (fieldId: string, isChecked: boolean) => {
    handleFieldChange(fieldId, isChecked)
  }

  const handleGroupToggle = (groupId: string) => {
    setExpandedGroup(expandedGroup === groupId ? null : groupId)
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

  const renderField = (fieldId: string, fieldConfig: FieldConfig, index: number) => {
    if (fieldId === 'enabled') {
      return null
    }

    return (
      <FormFieldRenderer
        key={fieldId}
        fieldId={fieldId}
        fieldConfig={fieldConfig}
        value={formData[fieldId]}
        allValues={formData}
        onChange={(value) => handleFieldChange(fieldId, value)}
        onToggle={handleToggleChange}
        isFirstGroup={index === 0 && fieldConfig.type === 'group'}
        isExpanded={fieldConfig.type === 'group' && expandedGroup === fieldId}
        onGroupToggle={handleGroupToggle}
        onFieldChange={handleFieldChange}
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
            {Object.entries(module.admin_settings).map(([fieldId, fieldConfig], index) =>
              renderField(fieldId, fieldConfig, index)
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
