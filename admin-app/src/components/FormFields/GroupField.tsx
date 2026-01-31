import { useState, useEffect, useRef, forwardRef } from '@wordpress/element'
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
  style?: React.CSSProperties
  isFirstGroup?: boolean
  isExpanded?: boolean
  onToggle?: (groupId: string) => void
  allValues?: Record<string, unknown>
  onFieldChange?: (fieldId: string, value: unknown) => void
}

const GroupField = forwardRef<HTMLDivElement, GroupFieldProps>(({
  id,
  config,
  value,
  onChange,
  className = '',
  style,
  isFirstGroup = false,
  isExpanded = false,
  onToggle,
  allValues = {},
  onFieldChange
}, ref) => {
  const contentRef = useRef<HTMLDivElement>(null)
  const fieldsRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    const el = contentRef.current
    if (!el || !fieldsRef.current) return

    const handleTransitionEnd = (e: TransitionEvent) => {
      if (e.propertyName === 'height' && isExpanded) {
        el.style.height = 'auto'
      }
    }

    el.addEventListener('transitionend', handleTransitionEnd)

    if (isExpanded) {
      const height = fieldsRef.current.scrollHeight
      el.style.height = `${height}px`
    } else {
      // Beim Zuklappen: Erst aktuelle Höhe setzen, dann auf 0 animieren
      if (el.style.height === 'auto') {
        el.style.height = `${el.scrollHeight}px`
        el.offsetHeight // Force reflow
      }
      el.style.height = '0px'
    }

    return () => {
      el.removeEventListener('transitionend', handleTransitionEnd)
    }
  }, [isExpanded])

  // Höhen-Update bei inneren Wrapper-Animationen (dependency-Änderungen)
  useEffect(() => {
    if (!isExpanded) return

    const fieldsEl = fieldsRef.current
    const contentEl = contentRef.current
    if (!fieldsEl || !contentEl) return

    const handleInnerTransitionEnd = (e: TransitionEvent) => {
      // Nur auf height-Transitionen von inneren Dependency-Wrappern reagieren
      if (e.propertyName !== 'height') return
      if (e.target === contentEl) return // Eigene Transition ignorieren

      // Wenn group-field-content auf auto steht, passt der Browser automatisch an
      if (contentEl.style.height === 'auto') return

      // Feste Pixel-Höhe → neu messen
      const height = fieldsEl.scrollHeight
      contentEl.style.height = `${height}px`
    }

    fieldsEl.addEventListener('transitionend', handleInnerTransitionEnd)

    return () => {
      fieldsEl.removeEventListener('transitionend', handleInnerTransitionEnd)
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
    <div ref={ref} className={`dvc-field group-field ${className} ${isExpanded ? 'expanded' : 'collapsed'}`} style={style}>
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
})

export default GroupField