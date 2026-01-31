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
  ImageField,
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
  const wrapperRef = useRef<HTMLDivElement>(null)
  const innerRef = useRef<HTMLDivElement>(null)

  const hasDependency = !!fieldConfig.depends_on
  const skipWrapperAnimation = fieldConfig.type === 'group'

  // Dependency-Check
  const isVisible = useMemo(() => {
    if (!fieldConfig.depends_on) return true

    if (typeof fieldConfig.depends_on === 'object') {
      return Object.entries(fieldConfig.depends_on).every(([dependentField, requiredValue]) => {
        const currentValue = allValues[dependentField]
        return currentValue === requiredValue
      })
    }

    if (typeof fieldConfig.depends_on === 'string') {
      console.warn('Legacy depends_on string format detected:', fieldConfig.depends_on)
      return true
    }

    return true
  }, [fieldConfig.depends_on, allValues, fieldId])

  // Wrapper-Animation (wie GroupField-Pattern)
  useEffect(() => {
    if (!hasDependency || skipWrapperAnimation) return

    const wrapper = wrapperRef.current
    const inner = innerRef.current
    if (!wrapper || !inner) return

    const handleTransitionEnd = (e: TransitionEvent) => {
      if (e.propertyName === 'height' && e.target === wrapper && isVisible) {
        wrapper.style.height = 'auto'
      }
    }

    wrapper.addEventListener('transitionend', handleTransitionEnd)

    if (isVisible) {
      const height = inner.scrollHeight
      wrapper.style.height = `${height}px`
    } else {
      if (wrapper.style.height === 'auto') {
        wrapper.style.height = `${wrapper.scrollHeight}px`
        wrapper.offsetHeight // Force reflow
      }
      wrapper.style.height = '0px'
    }

    return () => {
      wrapper.removeEventListener('transitionend', handleTransitionEnd)
    }
  }, [isVisible, hasDependency, skipWrapperAnimation])

  // Dependency Status Check
  const getDependencyStatus = () => {
    if (!fieldConfig.dependency_status) return '';
    return fieldConfig.dependency_status.supported ? '' : 'unsupported';
  }

  const commonProps = {
    id: fieldId,
    config: fieldConfig,
    value,
    onChange,
  }

  const dependencyClass = getDependencyStatus()

  const renderField = () => {
    switch (fieldConfig.type) {
      case 'text':
        return (
          <TextField
            {...commonProps}
            ref={innerRef}
            className={dependencyClass}
            value={(value as string) || ''}
            onChange={onChange}
          />
        )

      case 'textarea':
        return (
          <TextareaField
            {...commonProps}
            ref={innerRef}
            className={dependencyClass}
            value={(value as string) || ''}
            onChange={onChange}
          />
        )

      case 'toggle':
        return (
          <ToggleField
            {...commonProps}
            ref={innerRef}
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
            ref={innerRef}
            className={dependencyClass}
            value={(value as string) || ''}
            onChange={onChange}
          />
        )

      case 'multi_select':
        return (
          <MultiSelectField
            {...commonProps}
            ref={innerRef}
            className={dependencyClass}
            value={(value as string[]) || []}
            onChange={onChange}
          />
        )

      case 'number':
        return (
          <NumberField
            {...commonProps}
            ref={innerRef}
            className={dependencyClass}
            value={(value as number) || 0}
            onChange={onChange}
          />
        )

      case 'repeater':
        return (
          <RepeaterField
            {...commonProps}
            ref={innerRef}
            className={dependencyClass}
            value={(value as Record<string, unknown>[]) || []}
            onChange={onChange}
          />
        )

      case 'color':
        return (
          <ColorField
            {...commonProps}
            ref={innerRef}
            className={dependencyClass}
            value={(value as string) || ''}
            onChange={onChange}
          />
        )

      case 'image':
        return (
          <ImageField
            {...commonProps}
            ref={innerRef}
            className={dependencyClass}
            value={fieldConfig.multi
              ? ((value as number[]) || [])
              : ((value as number) || 0)}
            onChange={onChange}
          />
        )

      case 'group':
        return (
          <GroupField
            {...commonProps}
            ref={innerRef}
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

  // Felder mit depends_on bekommen einen Wrapper für die Clip-Animation
  if (hasDependency && !skipWrapperAnimation) {
    return (
      <div className="dvc-dependency-wrapper" ref={wrapperRef}>
        {renderField()}
      </div>
    )
  }

  return renderField()
}
