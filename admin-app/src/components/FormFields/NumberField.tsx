import { __experimentalNumberControl as NumberControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface NumberFieldProps {
  id: string
  config: FieldConfig
  value: number
  onChange: (value: number) => void
  className?: string
}

const NumberField: React.FC<NumberFieldProps> = ({
  id,
  config,
  value,
  onChange,
  className = ''
}) => {
  const min = config.validate?.min
  const max = config.validate?.max

  return (
    <div className={`dvc-field number-field ${className}`}>
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
}

export default NumberField
