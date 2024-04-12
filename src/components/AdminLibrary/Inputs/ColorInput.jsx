import React from 'react'

export default function ColorInput(props) {
  return (
    <div className={props.wrapperClass}>
        {props.description}
        <input
            className={props.inputClass}
            type="color"
            onChange={(e) => { props.onChange?.(e) }}
            value={props.value || '#000000'}
        />
    </div>
  )
}
