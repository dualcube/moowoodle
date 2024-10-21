import { useState, useEffect } from 'react';

const SimpleInput = (props) => {
    const { formField, onChange } = props;

    return (
        <div className='main-input-wrapper'>
            {/* Render label */}
            <input
                className='input-label simpleInput-label'
                type="text"
                value={formField.label}
                onChange={(event) => { onChange('label', event.target.value) }}
            />

            {/* Render Inputs */}
            <input
                className='input-text-section simpleInput-text-input'
                type="text"
                readOnly
                placeholder={formField.placeholder}
            />
        </div>
    )
}

export default SimpleInput;