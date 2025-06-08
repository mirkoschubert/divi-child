import { TextControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface TextFieldProps {
  id: string
  config: FieldConfig
  value: string
  onChange: (value: string) => void
  className?: string
}

const TextField: React.FC<TextFieldProps> = ({
  id,
  config,
  value,
  onChange,
  className = ''
}) => {
  return (
    <div className={`dvc-field text-field ${className}`}>
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
}

export default TextField
