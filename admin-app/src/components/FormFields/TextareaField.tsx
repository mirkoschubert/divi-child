import { TextareaControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface TextareaFieldProps {
  id: string
  config: FieldConfig
  value: string
  onChange: (value: string) => void
  className?: string
}

const TextareaField: React.FC<TextareaFieldProps> = ({
  id,
  config,
  value,
  onChange,
  className = ''
}) => {
  return (
    <div className={`dvc-field textarea-field ${className}`}>
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
}

export default TextareaField
