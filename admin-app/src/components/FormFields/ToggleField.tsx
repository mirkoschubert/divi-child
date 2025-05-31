import { ToggleControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface ToggleFieldProps {
  id: string
  config: FieldConfig
  value: boolean
  onChange: (value: boolean) => void
  onToggle?: (fieldId: string, isChecked: boolean) => void
}

const ToggleField: React.FC<ToggleFieldProps> = ({ 
  id, 
  config, 
  value, 
  onChange, 
  onToggle 
}) => {
  const handleChange = (checked: boolean) => {
    onChange(checked)
    onToggle?.(id, checked)
  }

  return (
    <div className="dvc-field toggle-field">
      <ToggleControl
        __nextHasNoMarginBottom
        label={config.label}
        help={config.description}
        checked={value}
        onChange={handleChange}
      />
    </div>
  )
}

export default ToggleField
