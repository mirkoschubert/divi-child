import { forwardRef } from '@wordpress/element'
import { CustomSelectControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface SelectFieldProps {
  id: string
  config: FieldConfig
  value: string
  onChange: (value: string) => void
  className?: string
  style?: React.CSSProperties
}

const SelectField = forwardRef<HTMLDivElement, SelectFieldProps>(({
  id,
  config,
  value,
  onChange,
  className = '',
  style
}, ref) => {
  const options = Object.entries(config.options || {}).map(([key, label]) => ({
    key,
    name: label,
  }))

  const selectedOption = options.find(opt => opt.key === value)

  return (
    <div ref={ref} className={`dvc-field select-field ${className}`} style={style}>
      <div className="dvc-field-header">
        <h4 className="dvc-field-label">{config.label}</h4>
        {config.description && (
          <p className="dvc-field-description">{config.description}</p>
        )}
      </div>

      <CustomSelectControl
        __next40pxDefaultSize
        hideLabelFromVision
        label={config.label || ''}
        value={selectedOption || options[0]}
        options={options}
        onChange={({ selectedItem }: { selectedItem: { key: string; name: string } }) => {
          onChange(selectedItem.key)
        }}
      />
    </div>
  )
})

export default SelectField
