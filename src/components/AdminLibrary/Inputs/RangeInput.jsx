import React from 'react'

export default function RangeInput(props) {
  return (
    <div className={props.wrapperClass}>
        {props.description}
        <div className={props.subWrapperClass}>
            <input
                className={props.inputClass}
                id={props.id}
                type="range"
                min={props.min}
                max={props.max}
                value={props.value}
                onChange={(e) => { props.onChange?.(e) }}
            />
             {
                    props.proSetting && <span className="admin-pro-tag">pro</span>
                }
            <output className={props.outputClass}>{props.value ? props.value : 0}px</output>
        </div>
    </div>
  )
}
