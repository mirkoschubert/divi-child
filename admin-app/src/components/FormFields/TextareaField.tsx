import { TextareaControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface TextareaFieldProps {
  id: string
  config: FieldConfig
  value: string
  onChange: (value: string) => void
}

const TextareaField: React.FC<TextareaFieldProps> = ({
  id,
  config,
  value,
  onChange,
}) => {
  return (
    <div className="dvc-field textarea-field">
      <TextareaControl
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
