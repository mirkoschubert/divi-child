import { forwardRef } from 'react'
import { __experimentalNumberControl as NumberControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface NumberFieldProps {
  id: string
  config: FieldConfig
  value: number
  onChange: (value: number) => void
  className?: string
  style?: React.CSSProperties
}

const NumberField = forwardRef<HTMLDivElement, NumberFieldProps>(({
  id,
  config,
  value,
  onChange,
  className = '',
  style
}, ref) => {
  const min = config.validate?.min
  const max = config.validate?.max

  return (
    <div ref={ref} className={`dvc-field number-field ${className}`} style={style}>
      <NumberControl
        __next40pxDefaultSize
        label={config.label}
        help={config.description}
        value={value}
        onChange={(val) => onChange(Number(val))}
        min={min}
        max={max}
      />
    </div>
  )
})

export default NumberField
