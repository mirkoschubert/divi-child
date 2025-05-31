import { SelectControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface SelectFieldProps {
  id: string
  config: FieldConfig
  value: string
  onChange: (value: string) => void
}

const SelectField: React.FC<SelectFieldProps> = ({
  id,
  config,
  value,
  onChange,
}) => {
  const options = Object.entries(config.options || {}).map(([key, label]) => ({
    label,
    value: key,
  }))

  return (
    <div className="dvc-field select-field">
      <SelectControl
        label={config.label}
        help={config.description}
        value={value}
        options={options}
        onChange={onChange}
      />
    </div>
  )
}

export default SelectField
