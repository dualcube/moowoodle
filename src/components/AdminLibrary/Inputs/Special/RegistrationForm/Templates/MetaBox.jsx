import React, { useState, useEffect } from "react";
import Draggable from 'react-draggable';

/**
 * Component that render form field type ( dropdown ) 
 */
const FormFieldSelect = (props) => {
    const { inputTypeList, formField, onChange } = props;

    // Find selected input type.
    const selectedInputType = inputTypeList.find((inputType) => inputType.value === formField.type);

    return (
        <article className="modal-content-section-field">
            <p>Type</p>
            <select onChange={(event) => { onChange?.(event.target.value) }}>
                {/* Render what is selected */}
                {
                    selectedInputType &&
                        <option>
                            <i className={`${selectedInputType.icon}`} />
                            <span>{selectedInputType.label}</span>
                        </option>
                }
                {
                    inputTypeList.map((inputType) => (
                        <option
                            className={`${inputType.value === formField.type ? 'selected' : ''}`}
                            value={inputType.value}
                        >
                            <i className={`${inputType.icon}`} />
                            {<span>{inputType.label}</span>}
                        </option>
                    ))
                }
            </select>
        </article>
    );
}

const SettingMetaBox = (props) => {
    const { formField, inputTypeList, onChange, onTypeChange } = props;

    const [hasOpened, setHasOpend] = useState(false);

    return (
        <div onClick={() => setHasOpend(true)}>
            <i className="admin-font font-menu"></i>
            {
                hasOpened &&
                    <Draggable>
                        <section className="meta-setting-modal">
                            {/* Render cross button */}
                            <button className="meta-setting-modal-button" onClick={(event) => {
                                event.stopPropagation();
                                setHasOpend(false);
                            }}>
                                <i className="admin-font font-cross"></i>
                            </button>
                            
                            {/* Render main components */}
                            <main className="meta-setting-modal-content">
                                <h3>Input Field Settings</h3>

                                <div className="setting-modal-content-section">
                                    <FormFieldSelect
                                        inputTypeList={inputTypeList}
                                        formField={formField}
                                        onChange={(type) => { onTypeChange?.(type) }}
                                    />

                                    {/* Set the name of input field */}
                                    <article className="modal-content-section-field">
                                        <p>Name</p>
                                        <input
                                            type="text"
                                            value={formField.name}
                                            onChange={(e) => onChange( 'name', e.target.value)}
                                        />
                                    </article>

                                    {
                                        ( formField.type == 'text' || formField.type == 'email' || formField.type == 'url' || formField.type == 'textarea' ) &&
                                            <article className="modal-content-section-field">
                                                <p>Placeholder</p>
                                                <input
                                                    type="text"
                                                    value={formField.placeholder}
                                                    onChange={(e) => onChange( 'placeholder', e.target.value)}
                                                />
                                            </article>
                                    }
                                </div>
                                <div className="setting-modal-content-section">
                                    {
                                        ( formField.type == 'text' || formField.type == 'email' || formField.type == 'url' || formField.type == 'textarea' ) &&
                                            <article className="modal-content-section-field">
                                                <p>Character limit</p>
                                                <input
                                                    type="number"
                                                    value={formField.charlimit}
                                                    onChange={(e) => onChange( 'charlimit', e.target.value)}
                                                />
                                            </article>
                                    }
                                    {
                                        ( formField.type == 'textarea' ) &&
                                            <article className="modal-content-section-field">
                                                <p>Row</p>
                                                <input
                                                    type="number"
                                                    value={formField.row}
                                                    onChange={(e) => onChange( 'row', e.target.value)}
                                                />
                                            </article>
                                    }
                                    {
                                        ( formField.type == 'recapta' ) &&
                                            <>
                                                <article className="modal-content-section-field">
                                                    <p>Api key</p>
                                                    <input
                                                        type="number"
                                                        value={formField.apikey}
                                                        onChange={(e) => onChange( 'apikey', e.target.value)}
                                                    />
                                                </article>

                                                <article className="modal-content-section-field">
                                                    <p>Site key</p>
                                                    <input
                                                        type="number"
                                                        value={formField.sitekey}
                                                        onChange={(e) => onChange( 'sitekey', e.target.value)}
                                                    />
                                                </article>
                                            </>
                                    }
                                    {
                                        ( formField.type == 'textarea' ) &&
                                            <article className="modal-content-section-field">
                                                <p>Column</p>
                                                <input
                                                    type="number"
                                                    value={formField.column}
                                                    onChange={(e) => onChange( 'column', e.target.value)}
                                                />
                                            </article>
                                    }
                                    {
                                        ( formField.type == 'attachment' ) &&
                                            <article className="modal-content-section-field">
                                                <p>Maximum file size</p>
                                                <input
                                                    type="number"
                                                    value={formField.filesize}
                                                    onChange={(e) => onChange( 'filesize', e.target.value ) }
                                                />
                                            </article>
                                    }
                                </div>
                                <div className="setting-modal-content-section">
                                    <article className="modal-content-section-field">
                                        <p>Visibility</p>
                                        <div className="visibility-control-container">
                                            <div className="tabs">
                                                <input checked={!formField.disabled} onChange={(e)=> onChange( 'disabled', !e.target.checked ) } type="radio" id="radio-1" name="tabs" />
                                                <label className="tab" htmlFor="radio-1">
                                                Visible
                                                </label>
                                                <input checked={formField.disabled} onChange={(e)=> onChange( 'disabled', e.target.checked ) } type="radio" id="radio-2" name="tabs" />
                                                <label className="tab" htmlFor="radio-2">
                                                Hidden
                                                </label>
                                                <span className="glider" />
                                            </div>
                                        </div>
                                    </article>
                                    <article className="modal-content-section-field">
                                        <p>Required</p>
                                        <input
                                            type="checkbox"
                                            checked={formField.required}
                                            onChange={(e) => onChange( 'required', e.target.checked ) }
                                        />
                                    </article>
                                </div>
                            </main>
                        </section>
                    </Draggable>
            }
        </div>
    );
}

export default SettingMetaBox;