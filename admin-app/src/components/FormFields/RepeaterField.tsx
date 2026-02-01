import { useState, useEffect, useRef, forwardRef } from '@wordpress/element'
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Flex,
  FlexItem,
} from '@wordpress/components'
import { __ } from '@wordpress/i18n'

import { FormFieldRenderer } from '../SettingsModal/FormFieldRenderer'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface RepeaterFieldProps {
  id: string
  config: FieldConfig
  value: Record<string, unknown>[]
  onChange: (value: Record<string, unknown>[]) => void
  className?: string
  style?: React.CSSProperties
}

const RepeaterField = forwardRef<HTMLDivElement, RepeaterFieldProps>(({
  id,
  config,
  value,
  onChange,
  className = '',
  style
}, ref) => {
  const [expandedItems, setExpandedItems] = useState<Set<number>>(new Set())
  const contentRefs = useRef<Map<number, HTMLDivElement>>(new Map())

  const addItem = () => {
    const newItem: Record<string, unknown> = {}

    // Initialisiere mit Standardwerten aus der Feldkonfiguration
    if (config.fields) {
      Object.entries(config.fields).forEach(([fieldId, fieldConfig]) => {
        newItem[fieldId] = fieldConfig.default || ''
      })
    }

    const updatedValue = [...value, newItem]
    onChange(updatedValue)

    // Neues Item automatisch aufklappen
    const newIndex = updatedValue.length - 1
    setExpandedItems((prev) => new Set([...prev, newIndex]))
  }

  const removeItem = (index: number) => {
    const updatedValue = value.filter((_, i) => i !== index)
    onChange(updatedValue)

    setExpandedItems((prev) => {
      const newSet = new Set(prev)
      newSet.delete(index)
      // Indices der nachfolgenden Items anpassen
      const adjustedSet = new Set<number>()
      newSet.forEach((i) => {
        if (i < index) {
          adjustedSet.add(i)
        } else if (i > index) {
          adjustedSet.add(i - 1)
        }
      })
      return adjustedSet
    })
  }

  const updateItem = (index: number, field: string, newValue: unknown) => {
    const updatedValue = [...value]
    updatedValue[index] = {
      ...updatedValue[index],
      [field]: newValue,
    }
    onChange(updatedValue)
  }

  // Expand/collapse Animation (GroupField-Pattern)
  useEffect(() => {
    contentRefs.current.forEach((el, index) => {
      if (!el) return

      if (expandedItems.has(index)) {
        el.style.height = `${el.scrollHeight}px`
      } else {
        if (el.style.height === 'auto') {
          el.style.height = `${el.scrollHeight}px`
          el.offsetHeight // Force reflow
        }
        el.style.height = '0px'
      }
    })
  }, [expandedItems])

  const toggleExpanded = (index: number) => {
    setExpandedItems((prev) => {
      const newSet = new Set(prev)
      if (newSet.has(index)) {
        newSet.delete(index)
      } else {
        newSet.add(index)
      }
      return newSet
    })
  }

  const getItemPreview = (item: Record<string, unknown>): string => {
    // Versuche ein aussagekräftiges Preview zu erstellen
    const previewFields = ['title', 'name', 'label', 'text', 'id']

    for (const field of previewFields) {
      if (item[field] && typeof item[field] === 'string') {
        const preview = String(item[field]).substring(0, 40)
        return preview.length < String(item[field]).length
          ? preview + '...'
          : preview
      }
    }

    // Fallback: Erstes verfügbares Feld
    const firstValue = Object.values(item)[0]
    if (firstValue && typeof firstValue === 'string') {
      const preview = firstValue.substring(0, 25)
      return preview.length < firstValue.length ? preview + '...' : preview
    }

    return __('Empty item', 'divi-child')
  }

  return (
    <div ref={ref} className={`dvc-field repeater-field ${className}`} style={style}>
      <div className="repeater-field-wrapper">
        {/* 🔧 Label und Description wie bei TextField */}
        <div className="dvc-field-header">
          <h4 className="dvc-field-label">
            {config.label}
          </h4>
          {config.description && (
            <p className="dvc-field-description">{config.description}</p>
          )}
        </div>

      <div className="repeater-items">
        {value.map((item, index) => {
          const isExpanded = expandedItems.has(index)
          const preview = getItemPreview(item)

          return (
            <Card
              key={index}
              className={`repeater-item ${
                isExpanded ? 'expanded' : 'collapsed'
              }`}
            >
              <CardHeader className="repeater-item-header">
                <Flex justify="space-between" align="center">
                  <FlexItem
                    className="repeater-item-toggle"
                    onClick={() => toggleExpanded(index)} // 🔧 Ganzer Bereich klickbar
                    style={{ cursor: 'pointer', flex: 1 }} // 🔧 Flex: 1 für vollen Platz
                  >
                    <Button
                      variant="tertiary"
                      onClick={(e: React.MouseEvent) => {
                        e.stopPropagation() // 🔧 Verhindert Doppel-Events
                        toggleExpanded(index)
                      }}
                      className="expand-toggle"
                    >
                      {/* 🔧 Besserer Pfeil mit mehr Abstand */}
                      <span className="expand-icon">
                        {isExpanded ? '▼' : '▶'}
                      </span>
                      <span className="item-label">
                        {preview || __('Empty item', 'divi-child')}
                      </span>
                    </Button>
                  </FlexItem>
                  <FlexItem>
                    <Button
                      variant="secondary"
                      isDestructive
                      onClick={(e: React.MouseEvent) => {
                        e.stopPropagation() // 🔧 Verhindert versehentliches Aufklappen
                        removeItem(index)
                      }}
                      className="remove-item-btn"
                      size="small"
                    >
                      {__('Remove', 'divi-child')}
                    </Button>
                  </FlexItem>
                </Flex>
              </CardHeader>

              <div
                className="repeater-item-content-wrapper"
                ref={(el) => {
                  if (el) {
                    contentRefs.current.set(index, el)
                  } else {
                    contentRefs.current.delete(index)
                  }
                }}
                style={{
                  height: 0,
                  overflow: 'hidden',
                  transition: 'height 0.3s ease-in-out'
                }}
                onTransitionEnd={(e) => {
                  if (e.propertyName === 'height' && isExpanded) {
                    const el = contentRefs.current.get(index)
                    if (el) el.style.height = 'auto'
                  }
                }}
              >
                <CardBody className="repeater-item-content">
                  <div className="repeater-fields">
                    {config.fields &&
                      Object.entries(config.fields).map(
                        ([fieldId, fieldConfig]) => (
                          <FormFieldRenderer
                            key={fieldId}
                            fieldId={fieldId}
                            fieldConfig={fieldConfig}
                            value={item[fieldId]}
                            allValues={item}
                            onChange={(newValue) =>
                              updateItem(index, fieldId, newValue)
                            }
                            onToggle={(fieldId, value) =>
                              updateItem(index, fieldId, value)
                            }
                          />
                        )
                      )}
                  </div>
                </CardBody>
              </div>
            </Card>
          )
        })}

        {/* Leerer Zustand */}
        {value.length === 0 && (
          <div className="repeater-empty">
            <p>{__('No items yet. Add your first item below.', 'divi-child')}</p>
          </div>
        )}
      </div>

        <Button
          variant="secondary"
          onClick={addItem}
          className="add-repeater-item"
        >
          {__('Add Item', 'divi-child')}
        </Button>
      </div>
    </div>
  )
})

export default RepeaterField
