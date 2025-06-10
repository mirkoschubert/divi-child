import { ColorPicker, ColorIndicator, Popover } from '@wordpress/components'
import { useState, useRef, forwardRef } from '@wordpress/element'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface ColorFieldProps {
  id: string
  config: FieldConfig
  value: string
  onChange: (value: string) => void
  className?: string
  style?: React.CSSProperties
}

const ColorField = forwardRef<HTMLDivElement, ColorFieldProps>((
  {
    id,
    config,
    value,
    onChange,
    className = '',
    style,
  },
  ref
) => {
  const [showPicker, setShowPicker] = useState(false)
  const indicatorRef = useRef<HTMLDivElement>(null)

  const handleIndicatorClick = () => {
    setShowPicker(!showPicker)
  }

  const handleColorChange = (color: string) => {
    onChange(color)
  }

  const handleClickOutside = () => {
    setShowPicker(false)
  }

  return (
    <div className={`dvc-field color-field ${className}`} ref={ref} style={style}>
      <div className="color-field-layout">
        <div className="color-field-info">
          <label className="dvc-field-label">
            {config.label}
          </label>
          {config.description && (
            <p className="dvc-field-description">{config.description}</p>
          )}
        </div>
        <div className="color-field-control">
          <div 
            ref={indicatorRef}
            className="color-indicator-wrapper"
            onClick={handleIndicatorClick}
          >
            <ColorIndicator 
              colorValue={value}
            />
          </div>
        </div>
      </div>
      
      {showPicker && indicatorRef.current && (
        <Popover
          anchor={indicatorRef.current}
          position="bottom left"
          onClose={handleClickOutside}
          className="color-field-popover"
        >
          <div className="color-field-picker">
            <ColorPicker
              color={value}
              onChange={handleColorChange}
              enableAlpha={false}
            />
          </div>
        </Popover>
      )}
    </div>
  )
})

export default ColorField