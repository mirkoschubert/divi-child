import { useState, useEffect, useRef } from '@wordpress/element'
import { chevronDown, chevronRight } from '@wordpress/icons'
import { Icon } from '@wordpress/components'
import type { FieldConfig } from '@/types'
import { FormFieldRenderer } from '../SettingsModal/FormFieldRenderer'

import './FormFields.styl'

interface GroupFieldProps {
  id: string
  config: FieldConfig
  value: Record<string, unknown>
  onChange: (value: unknown) => void
  className?: string
  isFirstGroup?: boolean
  isExpanded?: boolean
  onToggle?: (groupId: string) => void
  allValues?: Record<string, unknown>
  onFieldChange?: (fieldId: string, value: unknown) => void
}

const GroupField: React.FC<GroupFieldProps> = ({
  id,
  config,
  value,
  onChange,
  className = '',
  isFirstGroup = false,
  isExpanded = false,
  onToggle,
  allValues = {},
  onFieldChange
}) => {
  const contentRef = useRef<HTMLDivElement>(null)
  const fieldsRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (contentRef.current && fieldsRef.current) {
      if (isExpanded) {
        const height = fieldsRef.current.scrollHeight
        contentRef.current.style.height = `${height}px`
      } else {
        contentRef.current.style.height = '0px'
      }
    }
  }, [isExpanded])

  const toggleExpanded = () => {
    if (onToggle) {
      onToggle(id)
    }
  }

  const handleFieldChange = (fieldId: string, newValue: unknown) => {
    // For groups, we pass the change up to the parent (SettingsModal) 
    // which handles the flat data structure
    if (onFieldChange) {
      onFieldChange(fieldId, newValue)
    }
  }

  return (
    <div className={`dvc-field group-field ${className} ${isExpanded ? 'expanded' : 'collapsed'}`}>
      <div 
        className="group-field-header"
        onClick={toggleExpanded}
      >
        <div className="group-field-info">
          <h3 className="group-field-title">{config.title || config.label}</h3>
          {config.description && (
            <p className="group-field-description">{config.description}</p>
          )}
        </div>
        <div className="group-field-toggle">
          <Icon 
            icon={isExpanded ? chevronDown : chevronRight}
            size={20}
          />
        </div>
      </div>
      
      <div className="group-field-content" ref={contentRef}>
        <div className="group-field-fields" ref={fieldsRef}>
          {config.fields && Object.entries(config.fields).map(([fieldId, fieldConfig]) => (
            <FormFieldRenderer
              key={fieldId}
              fieldId={fieldId}
              fieldConfig={fieldConfig as FieldConfig}
              value={allValues[fieldId]}
              allValues={allValues}
              onChange={(newValue) => handleFieldChange(fieldId, newValue)}
              onFieldChange={onFieldChange}
            />
          ))}
        </div>
      </div>
    </div>
  )
}

export default GroupField