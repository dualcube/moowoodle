import React, { useState, useEffect } from "react";
import Draggable from 'react-draggable';

const OPtionMetaBox = (props) => {
    const { option, onChange, setDefaultValue, hasOpen } = props;

    const [hasOpened, setHasOpend] = useState(hasOpen);

    useEffect(() => {
        setHasOpend(hasOpen);
    }, [hasOpen]);

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
                                    {/* Set the name of input field */}
                                    <article className="modal-content-section-field">
                                        <p>Value</p>
                                        <input
                                            type="text"
                                            value={option.value}
                                            onChange={(e) => onChange( 'value', e.target.value)}
                                        />
                                    </article>

                                    <article className="modal-content-section-field">
                                        <p>Label</p>
                                        <input
                                            type="text"
                                            value={option.label}
                                            onChange={(e) => onChange( 'label', e.target.value)}
                                        />
                                    </article>
                                </div>
                                <div className="setting-modal-content-section">
                                    <article className="modal-content-section-field">
                                        <p>Set default</p>
                                        <input
                                            type="checkbox"
                                            checked={option.isdefault}
                                            onChange={(e) => setDefaultValue() }
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

export default OPtionMetaBox;