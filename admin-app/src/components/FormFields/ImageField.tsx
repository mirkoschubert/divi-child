import { Button, Spinner } from '@wordpress/components'
import { useState, useEffect, useCallback, useRef, forwardRef } from '@wordpress/element'
import { __ } from '@wordpress/i18n'
import type { FieldConfig, WpMediaAttachment } from '@/types'

import './FormFields.styl'

interface AttachmentData {
  id: number
  filename: string
  thumbnailUrl: string
}

interface ImageFieldProps {
  id: string
  config: FieldConfig
  value: number | number[]
  onChange: (value: number | number[] | null) => void
  className?: string
  style?: React.CSSProperties
}

const ImageField = forwardRef<HTMLDivElement, ImageFieldProps>((
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
  const isMulti = config.multi ?? false
  const [attachments, setAttachments] = useState<AttachmentData[]>([])
  const [loading, setLoading] = useState(false)
  const frameRef = useRef<ReturnType<typeof window.wp.media> | null>(null)

  const ids: number[] = isMulti
    ? (Array.isArray(value) ? value : value ? [Number(value)] : [])
    : (value ? [Number(value)] : [])

  useEffect(() => {
    if (ids.length === 0) {
      setAttachments([])
      return
    }
    setLoading(true)
    Promise.all(
      ids.map((attId) =>
        window.wp
          .apiFetch({ path: `/wp/v2/media/${attId}` })
          .then((media: any) => ({
            id: media.id,
            filename: media.title?.rendered || media.slug || `#${media.id}`,
            thumbnailUrl:
              media.media_details?.sizes?.thumbnail?.source_url ||
              media.media_details?.sizes?.medium?.source_url ||
              media.source_url ||
              '',
          }))
          .catch(() => null)
      )
    ).then((results) => {
      setAttachments(results.filter(Boolean) as AttachmentData[])
      setLoading(false)
    })
  }, [JSON.stringify(ids)])

  const openMedia = useCallback(() => {
    if (frameRef.current) {
      frameRef.current.open()
      return
    }

    const frame = window.wp.media({
      title: config.label || __('Select Image', 'divi-child'),
      multiple: isMulti,
      library: { type: 'image' },
      button: {
        text: isMulti
          ? __('Select Images', 'divi-child')
          : __('Select Image', 'divi-child'),
      },
    })

    frame.on('select', () => {
      const selection = frame.state().get('selection')
      if (isMulti) {
        const selected = selection.toJSON()
        const newIds = selected.map((att: WpMediaAttachment) => att.id)
        const merged = [...ids, ...newIds.filter((nid: number) => !ids.includes(nid))]
        onChange(merged)
      } else {
        const att = selection.first()
        onChange(att.id)
      }
    })

    frameRef.current = frame
    frame.open()
  }, [isMulti, ids, config.label, onChange])

  const removeAttachment = useCallback(
    (removeId: number) => {
      if (isMulti) {
        const newIds = ids.filter((i) => i !== removeId)
        onChange(newIds.length > 0 ? newIds : [])
      } else {
        onChange(null)
      }
    },
    [isMulti, ids, onChange]
  )

  return (

    <div ref={ref} className={`dvc-field image-field ${className}`} style={style}>
      <div className="image-field-header">
        <div className="image-field-info">
          {config.label && <h4 className="dvc-field-label">{config.label}</h4>}
          {config.description && (
            <p className="dvc-field-description">{config.description}</p>
          )}
        </div>
        <Button variant="secondary" onClick={openMedia} size="compact">
          {attachments.length > 0
            ? isMulti
              ? __('Add More', 'divi-child')
              : __('Replace', 'divi-child')
            : isMulti
              ? __('Select Images', 'divi-child')
              : __('Select Image', 'divi-child')}
        </Button>
      </div>

      {loading ? (
        <Spinner />
      ) : attachments.length > 0 ? (
        <div className={`image-field-preview ${isMulti ? 'multi' : 'single'}`}>
          {attachments.map((att) => (
            <div key={att.id} className="image-field-item">
              <img src={att.thumbnailUrl} alt={att.filename} />
              <Button
                className="image-field-remove"
                icon="no-alt"
                size="small"
                isDestructive
                label={__('Remove', 'divi-child')}
                onClick={() => removeAttachment(att.id)}
              />
            </div>
          ))}
        </div>
      ) : null}
    </div>
  )
})

export default ImageField
