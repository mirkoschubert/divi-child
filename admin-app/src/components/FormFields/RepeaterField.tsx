import { useState } from '@wordpress/element'
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
}

const RepeaterField: React.FC<RepeaterFieldProps> = ({
  id,
  config,
  value,
  onChange,
}) => {
  const [expandedItems, setExpandedItems] = useState<Set<number>>(new Set())

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

    // Expanded-Status für gelöschtes Item entfernen
    setExpandedItems((prev) => {
      const newSet = new Set(prev)
      newSet.delete(index)
      // Indices der nachfolgenden Items anpassen
      const adjustedSet = new Set()
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
    const previewFields = ['title', 'name', 'label', 'text']

    for (const field of previewFields) {
      if (item[field] && typeof item[field] === 'string') {
        const preview = String(item[field]).substring(0, 50)
        return preview.length < String(item[field]).length
          ? preview + '...'
          : preview
      }
    }

    // Fallback: Erstes verfügbares Feld
    const firstValue = Object.values(item)[0]
    if (firstValue && typeof firstValue === 'string') {
      const preview = firstValue.substring(0, 30)
      return preview.length < firstValue.length ? preview + '...' : preview
    }

    return __('Item', 'divi-child')
  }

  return (
    <div className="dvc-field repeater-field">
      <label className="dvc-field-label">
        {config.label}
        {config.description && (
          <p className="dvc-field-description">{config.description}</p>
        )}
      </label>

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
              <CardHeader>
                <Flex justify="space-between" align="center">
                  <FlexItem>
                    <Button
                      variant="tertiary"
                      onClick={() => toggleExpanded(index)}
                      className="expand-toggle"
                    >
                      <strong>
                        {__('Item', 'divi-child')} {index + 1}
                      </strong>
                      {!isExpanded && (
                        <span className="item-preview">: {preview}</span>
                      )}
                      <span className="dashicon">{isExpanded ? '↑' : '↓'}</span>
                    </Button>
                  </FlexItem>
                  <FlexItem>
                    <Button
                      variant="secondary"
                      isDestructive
                      onClick={() => removeItem(index)}
                      className="remove-item-btn"
                    >
                      {__('Remove', 'divi-child')}
                    </Button>
                  </FlexItem>
                </Flex>
              </CardHeader>

              {isExpanded && (
                <CardBody>
                  <div className="repeater-fields">
                    {config.fields &&
                      Object.entries(config.fields).map(
                        ([fieldId, fieldConfig]) => (
                          <FormFieldRenderer
                            key={fieldId}
                            fieldId={fieldId}
                            fieldConfig={fieldConfig}
                            value={item[fieldId]}
                            onChange={(newValue) =>
                              updateItem(index, fieldId, newValue)
                            }
                          />
                        )
                      )}
                  </div>
                </CardBody>
              )}
            </Card>
          )
        })}
      </div>

      <Button
        variant="secondary"
        onClick={addItem}
        className="add-repeater-item"
      >
        {__('Add Item', 'divi-child')}
      </Button>
    </div>
  )
}

export default RepeaterField
