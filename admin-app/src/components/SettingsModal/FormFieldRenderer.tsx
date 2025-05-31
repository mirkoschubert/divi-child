import {
  TextField,
  ToggleField,
  SelectField,
  NumberField,
  ListField,
  RepeaterField,
  TextareaField,
} from '../FormFields'
import type { FieldConfig } from '@/types'

interface FormFieldRendererProps {
  fieldId: string
  fieldConfig: FieldConfig
  value: unknown
  onChange: (value: unknown) => void
  onToggle?: (fieldId: string, isChecked: boolean) => void
}

export const FormFieldRenderer: React.FC<FormFieldRendererProps> = ({
  fieldId,
  fieldConfig,
  value,
  onChange,
  onToggle,
}) => {
  const commonProps = {
    id: fieldId,
    config: fieldConfig,
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
          value={(value as boolean) || false}
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

    case 'number':
      return (
        <NumberField
          {...commonProps}
          value={(value as number) || 0}
          onChange={onChange}
        />
      )

    case 'list':
      return (
        <ListField
          {...commonProps}
          value={(value as string[]) || []}
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

    default:
      return (
        <div className="dvc-field">
          <p>Unsupported field type: {fieldConfig.type}</p>
        </div>
      )
  }
}
