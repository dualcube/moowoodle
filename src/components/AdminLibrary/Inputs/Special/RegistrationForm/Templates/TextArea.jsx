import { useState, useEffect } from 'react';

const Textarea = (props) => {
    const { formField, onChange } = props;

    return (
        <>
            <div className='main-input-wrapper'>
                {/* Render label */}
                <input
                    className='input-label textArea-label'
                    type="text"
                    value={formField.label}
                    placeholder={"I am label"}
                    onChange={(event) => {
                        onChange('label', event.target.value);
                    }}
                />

                {/* Render placeholder */}
                <input
                    className='input-text-section textArea-text-input'
                    type="text"
                    value={formField.placeholder}
                    placeholder={"I am input placeholder"}
                    readOnly={true}
                />
            </div>
        </>
    )
}

export default Textarea;