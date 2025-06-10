import { forwardRef } from 'react'
import { TextareaControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface TextareaFieldProps {
  id: string
  config: FieldConfig
  value: string
  onChange: (value: string) => void
  className?: string
  style?: React.CSSProperties
}

const TextareaField = forwardRef<HTMLDivElement, TextareaFieldProps>(({
  id,
  config,
  value,
  onChange,
  className = '',
  style
}, ref) => {
  return (
    <div ref={ref} className={`dvc-field textarea-field ${className}`} style={style}>
      <TextareaControl
        __nextHasNoMarginBottom
        label={config.label}
        help={config.description}
        value={value}
        onChange={onChange}
        rows={5}
      />
    </div>
  )
})

export default TextareaField
