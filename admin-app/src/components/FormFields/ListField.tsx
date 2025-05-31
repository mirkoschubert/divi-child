import { useState } from '@wordpress/element'
import { Button, TextControl, Flex, FlexItem } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import type { FieldConfig } from '@/types'

import './FormFields.styl'

interface ListFieldProps {
  id: string
  config: FieldConfig
  value: string[]
  onChange: (value: string[]) => void
}

const ListField: React.FC<ListFieldProps> = ({
  id,
  config,
  value,
  onChange,
}) => {
  const [newItem, setNewItem] = useState('')

  const addItem = () => {
    if (newItem.trim()) {
      const updatedList = [...value, newItem.trim()]
      onChange(updatedList)
      setNewItem('')
    }
  }

  const removeItem = (index: number) => {
    const updatedList = value.filter((_, i) => i !== index)
    onChange(updatedList)
  }

  const updateItem = (index: number, newValue: string) => {
    const updatedList = [...value]
    updatedList[index] = newValue
    onChange(updatedList)
  }

  const handleKeyPress = (event: React.KeyboardEvent) => {
    if (event.key === 'Enter') {
      event.preventDefault()
      addItem()
    }
  }

  return (
    <div className="dvc-field list-field">
      <label className="dvc-field-label">
        {config.label}
        {config.description && (
          <p className="dvc-field-description">{config.description}</p>
        )}
      </label>

      {/* Bestehende Items */}
      <div className="list-items">
        {value.map((item, index) => (
          <Flex key={index} className="list-item" gap={2}>
            <FlexItem>
              <TextControl
                __nextHasNoMarginBottom
                __next40pxDefaultSize
                value={item}
                onChange={(newValue) => updateItem(index, newValue)}
                placeholder={__('Enter value...', 'divi-child')}
              />
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
        ))}
      </div>

      {/* Neues Item hinzufügen */}
      <Flex className="add-item-section" gap={2}>
        <FlexItem>
          <TextControl
            __nextHasNoMarginBottom
            __next40pxDefaultSize
            value={newItem}
            onChange={setNewItem}
            placeholder={__('Add new item...', 'divi-child')}
            onKeyPress={handleKeyPress}
          />
        </FlexItem>
        <FlexItem>
          <Button
            variant="primary"
            onClick={addItem}
            disabled={!newItem.trim()}
          >
            {__('Add', 'divi-child')}
          </Button>
        </FlexItem>
      </Flex>
    </div>
  )
}

export default ListField
