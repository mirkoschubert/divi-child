import { useState, useEffect } from '@wordpress/element'
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Flex,
  FlexItem,
  CheckboxControl,
  Spinner,
  Notice
} from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import apiFetch from '@wordpress/api-fetch'

import './FormFields.styl'

interface FontSelectorFieldProps {
  id: string
  config: {
    label: string
    description?: string
    default?: Record<string, { weights: string[] }>
  }
  value: Record<string, { weights: string[] }>
  onChange: (value: Record<string, { weights: string[] }>) => void
}

interface GoogleFont {
  weights: string[]
}

const FontSelectorField: React.FC<FontSelectorFieldProps> = ({
  id,
  config,
  value,
  onChange,
}) => {
  const [availableFonts, setAvailableFonts] = useState<Record<string, GoogleFont>>({})
  const [isLoading, setIsLoading] = useState(true)
  const [isDownloading, setIsDownloading] = useState(false)
  const [downloadMessage, setDownloadMessage] = useState<{ type: 'success' | 'error', text: string } | null>(null)

  useEffect(() => {
    loadFontsList()
  }, [])

  const loadFontsList = async () => {
    try {
      const response = await apiFetch({
        path: '/divi-child/v1/modules/localfonts/fonts/list',
        method: 'GET'
      }) as { success: boolean, data: Record<string, GoogleFont> }

      if (response.success) {
        setAvailableFonts(response.data)
      }
    } catch (error) {
      console.error('Error loading fonts:', error)
    } finally {
      setIsLoading(false)
    }
  }

  const handleFontToggle = (fontFamily: string, checked: boolean) => {
    const newValue = { ...value }
    
    if (checked) {
      // Alle Weights standardmäßig auswählen
      newValue[fontFamily] = {
        weights: availableFonts[fontFamily]?.weights || []
      }
    } else {
      delete newValue[fontFamily]
    }
    
    onChange(newValue)
  }

  const handleWeightToggle = (fontFamily: string, weight: string, checked: boolean) => {
    const newValue = { ...value }
    
    if (!newValue[fontFamily]) {
      newValue[fontFamily] = { weights: [] }
    }
    
    if (checked) {
      if (!newValue[fontFamily].weights.includes(weight)) {
        newValue[fontFamily].weights.push(weight)
      }
    } else {
      newValue[fontFamily].weights = newValue[fontFamily].weights.filter(w => w !== weight)
      
      // Font entfernen wenn keine Weights mehr ausgewählt
      if (newValue[fontFamily].weights.length === 0) {
        delete newValue[fontFamily]
      }
    }
    
    onChange(newValue)
  }

  const downloadFonts = async () => {
    if (Object.keys(value).length === 0) {
      setDownloadMessage({
        type: 'error',
        text: __('Please select at least one font.', 'divi-child')
      })
      return
    }

    setIsDownloading(true)
    setDownloadMessage(null)

    try {
      const response = await apiFetch({
        path: '/divi-child/v1/modules/localfonts/fonts/download',
        method: 'POST',
        data: {
          selected_fonts: value
        }
      }) as { success: boolean, data: string }

      if (response.success) {
        setDownloadMessage({
          type: 'success',
          text: response.data
        })
      } else {
        setDownloadMessage({
          type: 'error',
          text: response.data || __('Download failed', 'divi-child')
        })
      }
    } catch (error) {
      setDownloadMessage({
        type: 'error',
        text: __('Network error during download', 'divi-child')
      })
    } finally {
      setIsDownloading(false)
    }
  }

  if (isLoading) {
    return (
      <div className="dvc-field">
        <label>{config.label}</label>
        <div style={{ textAlign: 'center', padding: '20px' }}>
          <Spinner />
          <p>{__('Loading fonts...', 'divi-child')}</p>
        </div>
      </div>
    )
  }

  return (
    <div className="dvc-field">
      <label htmlFor={id}>{config.label}</label>
      {config.description && (
        <p className="description">{config.description}</p>
      )}

      <div className="font-selector">
        {downloadMessage && (
          <Notice 
            status={downloadMessage.type} 
            isDismissible={true}
            onRemove={() => setDownloadMessage(null)}
          >
            {downloadMessage.text}
          </Notice>
        )}

        <div className="font-list">
          {Object.entries(availableFonts).map(([fontFamily, fontData]) => {
            const isSelected = !!value[fontFamily]
            const selectedWeights = value[fontFamily]?.weights || []

            return (
              <Card key={fontFamily} className="font-item">
                <CardHeader>
                  <CheckboxControl
                    label={
                      <span style={{ fontFamily: `'${fontFamily}', sans-serif`, fontSize: '16px' }}>
                        {fontFamily}
                      </span>
                    }
                    checked={isSelected}
                    onChange={(checked) => handleFontToggle(fontFamily, checked)}
                  />
                </CardHeader>
                
                {isSelected && (
                  <CardBody>
                    <div className="font-weights">
                      <label>{__('Weights:', 'divi-child')}</label>
                      <Flex wrap>
                        {fontData.weights.map((weight) => (
                          <FlexItem key={weight}>
                            <CheckboxControl
                              label={weight}
                              checked={selectedWeights.includes(weight)}
                              onChange={(checked) => handleWeightToggle(fontFamily, weight, checked)}
                            />
                          </FlexItem>
                        ))}
                      </Flex>
                    </div>
                  </CardBody>
                )}
              </Card>
            )
          })}
        </div>

        <div className="font-actions" style={{ marginTop: '20px' }}>
          <Button
            variant="primary"
            onClick={downloadFonts}
            isBusy={isDownloading}
            disabled={isDownloading || Object.keys(value).length === 0}
          >
            {isDownloading 
              ? __('Downloading...', 'divi-child')
              : __('Download Selected Fonts', 'divi-child')
            }
          </Button>
          
          {Object.keys(value).length > 0 && (
            <p style={{ marginTop: '10px', fontSize: '14px', color: '#666' }}>
              {__('Selected:', 'divi-child')} {Object.keys(value).length} {__('fonts', 'divi-child')}
            </p>
          )}
        </div>
      </div>
    </div>
  )
}

export default FontSelectorField