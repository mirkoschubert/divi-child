import { useState } from '@wordpress/element'
import { FormTokenField } from '@wordpress/components'
import { __ } from '@wordpress/i18n'

interface MultiSelectFieldProps {
  id: string
  config: {
    label: string
    description?: string
    default?: string[]
    options?: Record<string, string>
  }
  value: string[]
  onChange: (value: string[]) => void
}

const MultiSelectField: React.FC<MultiSelectFieldProps> = ({
  id,
  config,
  value = [],
  onChange,
}) => {
  const [searchTerm, setSearchTerm] = useState('')

  // Options zu Suggestions umwandeln
  const options = config.options || {}
  const suggestions = Object.keys(options)

  // Filtere Suggestions basierend auf Suchterm
  const filteredSuggestions = suggestions.filter(
    (key) =>
      key.toLowerCase().includes(searchTerm.toLowerCase()) ||
      options[key].toLowerCase().includes(searchTerm.toLowerCase())
  )

  // Transformiere values für Anzeige
  const displayValues = value.map((val) => options[val] || val)

  const handleChange = (newValues: string[]) => {
    // Transformiere Display-Werte zurück zu Keys
    const actualValues = newValues.map((displayVal) => {
      const key = Object.keys(options).find((k) => options[k] === displayVal)
      return key || displayVal
    })
    onChange(actualValues)
  }

  return (
    <div className="dvc-field multi-select-field">
      <label htmlFor={id}>{config.label}</label>
      {config.description && (
        <p className="description">{config.description}</p>
      )}

      <FormTokenField
        value={displayValues}
        suggestions={filteredSuggestions.map((key) => options[key])}
        onChange={handleChange}
        placeholder={__('Search and select...', 'divi-child')}
        maxSuggestions={200}
        __next40pxDefaultSize
        __nextHasNoMarginBottom
        __experimentalExpandOnFocus
      />
    </div>
  )
}

export default MultiSelectField
