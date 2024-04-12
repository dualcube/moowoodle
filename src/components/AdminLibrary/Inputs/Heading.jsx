import React from 'react'

export default function Heading(props) {
  return (
    <div className={props.wrapperClass}>
        {
            inputField.blocktext &&
            <h5 dangerouslySetInnerHTML={{ __html: inputField.blocktext }}></h5>
        }
    </div>
  )
}
