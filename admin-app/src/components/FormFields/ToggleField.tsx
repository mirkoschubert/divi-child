import { forwardRef } from 'react'
import { ToggleControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface ToggleFieldProps {
  id: string
  config: FieldConfig
  value: boolean
  onChange: (value: boolean) => void
  onToggle?: (fieldId: string, isChecked: boolean) => void
  className?: string
  style?: React.CSSProperties
}

const ToggleField = forwardRef<HTMLDivElement, ToggleFieldProps>(({ 
  id, 
  config, 
  value, 
  onChange, 
  onToggle,
  className = '',
  style
}, ref) => {
  const handleChange = (checked: boolean) => {
    onChange(checked)
    onToggle?.(id, checked)
  }

  return (
    <div ref={ref} className={`dvc-field toggle-field ${className}`} style={style}>
      <ToggleControl
        __nextHasNoMarginBottom
        label={config.label}
        help={config.description}
        checked={value}
        onChange={handleChange}
      />
    </div>
  )
})

export default ToggleField
