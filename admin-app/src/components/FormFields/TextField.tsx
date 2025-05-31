import { TextControl } from '@wordpress/components'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface TextFieldProps {
  id: string
  config: FieldConfig
  value: string
  onChange: (value: string) => void
}

const TextField: React.FC<TextFieldProps> = ({
  id,
  config,
  value,
  onChange,
}) => {
  return (
    <div className="dvc-field text-field">
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
