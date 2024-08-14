const MultiCheckBox = (props) => {
    return (
        <div className={props.wrapperClass} >
            {
                props.selectDeselect &&
                <>
                    <button
                        className={props.selectDeselectClass}
                        onClick={(e) => { 
                            e.preventDefault();
                            props.onMultiSelectDeselectChange?.(e) 
                        }}
                    >
                        {props.selectDeselectValue}
                    </button>
                </>
            }
            <div className="wrapper">
                {
                    props.options.map((option) => {
                        let checked = false;
                        if (props.value && props.value.length > 0) {
                            checked = props.value.indexOf(option.value) >= 0;
                        }
                        return (
                            <div className={props.inputWrapperClass}>
                                {
                                    props.rightContent &&
                                    <p className={props.rightContentClass} dangerouslySetInnerHTML={{ __html: option.label }} ></p>
                                }
                                <div className={props.inputInnerWrapperClass}>
                                    <input
                                        className={props.inputClass}
                                        id={`${props.idPrefix}-${option.key}`}
                                        key={option.key}
                                        type={props.type || 'checkbox'}
                                        name={option.name || 'basic-input'}
                                        value={option.value}
                                        checked={checked}
                                        onChange={(e) => {
                                            if (option.proSetting && !appLocalizer.pro_active) {
                                                return props.proChanged();
                                            }
                                            props.onChange?.(e)
                                        }}
                                    />
                                    <label htmlFor={`${props.idPrefix}-${option.key}`}></label>
                                    {
                                        props.proSetting && <span className="admin-pro-tag">pro</span>
                                    }
                                </div>
                                {
                                    !props.rightContent &&
                                    <p className={props.rightContentClass} dangerouslySetInnerHTML={{ __html: option.label }} ></p>
                                }
                                {
                                    option.hints &&
                                    <span className={props.hintOuterClass} dangerouslySetInnerHTML={{__html: option.hints}}></span>
                                }
                                {
                                    (option.proSetting && !appLocalizer.pro_active) && <span className="admin-pro-tag">pro</span>
                                }
                            </div>
                        );
                    })
                }
            </div>
            {
                props.description &&
                <p className={props.descClass} dangerouslySetInnerHTML={{ __html: props.description }}>
                </p>
            }
        </div>
    );
}

export default MultiCheckBox;