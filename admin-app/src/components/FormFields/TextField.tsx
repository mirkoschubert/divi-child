import { forwardRef } from 'react'
import { TextControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface TextFieldProps {
  id: string
  config: FieldConfig
  value: string
  onChange: (value: string) => void
  className?: string
  style?: React.CSSProperties
}

const TextField = forwardRef<HTMLDivElement, TextFieldProps>(({
  id,
  config,
  value,
  onChange,
  className = '',
  style
}, ref) => {
  return (
    <div ref={ref} className={`dvc-field text-field ${className}`} style={style}>
      <TextControl
        __nextHasNoMarginBottom
        __next40pxDefaultSize
        label={config.label}
        help={config.description}
        value={value}
        onChange={onChange}
      />
    </div>
  )
})

export default TextField
