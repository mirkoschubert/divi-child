import { forwardRef } from 'react'
import { SelectControl } from '@wordpress/components'
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
    label,
    value: key,
  }))

  return (
    <div ref={ref} className={`dvc-field select-field ${className}`} style={style}>
      <SelectControl
        __next40pxDefaultSize
        __nextHasNoMarginBottom
        label={config.label}
        help={config.description}
        value={value}
        options={options}
        onChange={onChange}
      />
    </div>
  )
})

export default SelectField
