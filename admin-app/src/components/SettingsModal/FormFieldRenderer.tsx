import {
  TextField,
  ToggleField,
  SelectField,
  MultiSelectField,
  NumberField,
  RepeaterField,
  TextareaField,
  FontSelectorField,
} from '../FormFields'
import type { FieldConfig } from '@/types'
import { useMemo } from 'react'

interface FormFieldRendererProps {
  fieldId: string
  fieldConfig: FieldConfig
  value: unknown
  allValues: Record<string, unknown>
  onChange: (value: unknown) => void
  onToggle?: (fieldId: string, isChecked: boolean) => void
}

export const FormFieldRenderer: React.FC<FormFieldRendererProps> = ({
  fieldId,
  fieldConfig,
  value,
  allValues,
  onChange,
  onToggle,
}) => {
  // 🔧 Dependency-Check für die echte API-Struktur
  const isVisible = useMemo(() => {
    if (!fieldConfig.depends_on) {
      return true // Keine Dependencies = immer sichtbar
    }

    // Die API sendet depends_on immer als Objekt
    if (typeof fieldConfig.depends_on === 'object') {
      return Object.entries(fieldConfig.depends_on).every(([dependentField, requiredValue]) => {
        const currentValue = allValues[dependentField]
        return currentValue === requiredValue
      })
    }

    // Fallback für Legacy-Support (falls doch mal String kommt)
    if (typeof fieldConfig.depends_on === 'string') {
      console.warn('Legacy depends_on string format detected:', fieldConfig.depends_on)
      return true // Zeige an, wenn unsicher
    }

    return true
  }, [fieldConfig.depends_on, allValues, fieldId])

  // 🔧 Feld verstecken wenn Dependencies nicht erfüllt sind
  if (!isVisible) {
    return null
  }

  // Rest bleibt gleich...
  const commonProps = {
    id: fieldId,
    config: fieldConfig,
    value,
    onChange,
  }

  switch (fieldConfig.type) {
    case 'text':
      return (
        <TextField
          {...commonProps}
          value={(value as string) || ''}
          onChange={onChange}
        />
      )

    case 'textarea':
      return (
        <TextareaField
          {...commonProps}
          value={(value as string) || ''}
          onChange={onChange}
        />
      )

    case 'toggle':
      return (
        <ToggleField
          {...commonProps}
          value={Boolean(value)}
          onChange={onChange}
          onToggle={onToggle}
        />
      )

    case 'select':
      return (
        <SelectField
          {...commonProps}
          value={(value as string) || ''}
          onChange={onChange}
        />
      )

    case 'multi_select':
      return (
        <MultiSelectField
          {...commonProps}
          value={(value as Record<string, unknown>[]) || []}
          onChange={onChange}
        />
      )

    case 'number':
      return (
        <NumberField
          {...commonProps}
          value={(value as number) || 0}
          onChange={onChange}
        />
      )

    case 'repeater':
      return (
        <RepeaterField
          {...commonProps}
          value={(value as Record<string, unknown>[]) || []}
          onChange={onChange}
        />
      )

    case 'font_selector':
      return (
        <FontSelectorField
          {...commonProps}
          value={(value as Record<string, { weights: string[] }>) || {}}
          onChange={onChange}
        />
      )

    default:
      return (
        <div className="dvc-field">
          <p>Unsupported field type: {fieldConfig.type}</p>
        </div>
      )
  }
}
