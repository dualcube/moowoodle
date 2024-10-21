import { useState, useEffect } from 'react';

const Attachment = (props) => {
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

                {/* Render attachments */}
                <div className="attachment-section">
                    <label
                        htmlFor="dropzone-file"
                        className="attachment-label"
                    >
                        <div className="wrapper">
                        <svg
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 20 16"
                        >
                            <path
                            stroke="currentColor"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                            />
                        </svg>
                        <p className="heading">
                            <span>Click to upload</span> or drag and drop
                        </p>
                        </div>
                        <input readOnly id="dropzone-file" type="file" className="hidden" />
                    </label>
                    </div>

            </div>
        </>
    )
}

export default Attachment;