import {
  TextField,
  ToggleField,
  SelectField,
  MultiSelectField,
  NumberField,
  RepeaterField,
  TextareaField,
  ColorField,
  GroupField,
} from '../FormFields'
import type { FieldConfig } from '@/types'
import { useMemo, useEffect, useRef } from 'react'

interface FormFieldRendererProps {
  fieldId: string
  fieldConfig: FieldConfig
  value: unknown
  allValues: Record<string, unknown>
  onChange: (value: unknown) => void
  onToggle?: (fieldId: string, isChecked: boolean) => void
  isFirstGroup?: boolean
  isExpanded?: boolean
  onGroupToggle?: (groupId: string) => void
  onFieldChange?: (fieldId: string, value: unknown) => void
}

export const FormFieldRenderer: React.FC<FormFieldRendererProps> = ({
  fieldId,
  fieldConfig,
  value,
  allValues,
  onChange,
  onToggle,
  isFirstGroup = false,
  isExpanded = false,
  onGroupToggle,
  onFieldChange,
}) => {
  const fieldRef = useRef<HTMLDivElement>(null)
  // 🔧 Dependency-Check für die echte API-Struktur
  const isVisible = useMemo(() => {
    if (!fieldConfig.depends_on) {
      return true // Keine Dependencies = immer sichtbar
    }

    // Die API sendet depends_on immer als Objekt
    if (typeof fieldConfig.depends_on === 'object') {
      return Object.entries(fieldConfig.depends_on).every(([dependentField, requiredValue]) => {
        const currentValue = allValues[dependentField]
        return currentValue === requiredValue
      })
    }

    // Fallback für Legacy-Support (falls doch mal String kommt)
    if (typeof fieldConfig.depends_on === 'string') {
      console.warn('Legacy depends_on string format detected:', fieldConfig.depends_on)
      return true // Zeige an, wenn unsicher
    }

    return true
  }, [fieldConfig.depends_on, allValues, fieldId])

  useEffect(() => {
    // GroupField und RepeaterField haben ihre eigene Höhenlogik - nicht überschreiben
    if (fieldConfig.type === 'group' || fieldConfig.type === 'repeater') {
      return
    }
    
    const timer = setTimeout(() => {
      if (fieldRef.current) {
        if (isVisible) {
          fieldRef.current.style.height = 'auto'
          const height = fieldRef.current.scrollHeight
          fieldRef.current.style.height = '0px'
          // Force reflow
          fieldRef.current.offsetHeight
          fieldRef.current.style.height = `${height}px`
        } else {
          fieldRef.current.style.height = '0px'
        }
      }
    }, 0)

    return () => clearTimeout(timer)
  }, [isVisible, fieldConfig.type])

  // 🔧 Dependency Status Check
  const getDependencyStatus = () => {
    if (!fieldConfig.dependency_status) return '';
    return fieldConfig.dependency_status.supported ? '' : 'unsupported';
  }

  // Dependency-Message wird nicht mehr angezeigt - Requirements stehen in der Description

  // Rest bleibt gleich...
  const commonProps = {
    id: fieldId,
    config: fieldConfig,
    value,
    onChange,
  }

  const dependencyClass = getDependencyStatus()
  
  const fieldStyle = {
    overflow: 'hidden',
    transition: 'height 0.3s ease-in-out, opacity 0.3s ease-in-out',
    opacity: isVisible ? 1 : 0
  }

  switch (fieldConfig.type) {
    case 'text':
      return (
        <TextField
          {...commonProps}
          ref={fieldRef}
          style={fieldStyle}
          className={dependencyClass}
          value={(value as string) || ''}
          onChange={onChange}
        />
      )

    case 'textarea':
      return (
        <TextareaField
          {...commonProps}
          ref={fieldRef}
          style={fieldStyle}
          className={dependencyClass}
          value={(value as string) || ''}
          onChange={onChange}
        />
      )

    case 'toggle':
      return (
        <ToggleField
          {...commonProps}
          ref={fieldRef}
          style={fieldStyle}
          className={dependencyClass}
          value={Boolean(value)}
          onChange={onChange}
          onToggle={onToggle}
        />
      )

    case 'select':
      return (
        <SelectField
          {...commonProps}
          ref={fieldRef}
          style={fieldStyle}
          className={dependencyClass}
          value={(value as string) || ''}
          onChange={onChange}
        />
      )

    case 'multi_select':
      return (
        <MultiSelectField
          {...commonProps}
          ref={fieldRef}
          style={fieldStyle}
          className={dependencyClass}
          value={(value as string[]) || []}
          onChange={onChange}
        />
      )

    case 'number':
      return (
        <NumberField
          {...commonProps}
          ref={fieldRef}
          style={fieldStyle}
          className={dependencyClass}
          value={(value as number) || 0}
          onChange={onChange}
        />
      )

    case 'repeater':
      return (
        <RepeaterField
          {...commonProps}
          ref={fieldRef}
          style={fieldStyle}
          className={dependencyClass}
          value={(value as Record<string, unknown>[]) || []}
          onChange={onChange}
        />
      )

    case 'color':
      return (
        <ColorField
          {...commonProps}
          ref={fieldRef}
          style={fieldStyle}
          className={dependencyClass}
          value={(value as string) || ''}
          onChange={onChange}
        />
      )

    case 'group':
      return (
        <GroupField
          {...commonProps}
          ref={fieldRef}
          style={fieldStyle}
          className={dependencyClass}
          value={(value as Record<string, unknown>) || {}}
          onChange={onChange}
          isFirstGroup={isFirstGroup}
          isExpanded={isExpanded}
          onToggle={onGroupToggle}
          allValues={allValues}
          onFieldChange={onFieldChange}
        />
      )

    default:
      return (
        <div className={`dvc-field ${dependencyClass}`}>
          <p>Unsupported field type: {fieldConfig.type}</p>
        </div>
      )
  }
}
