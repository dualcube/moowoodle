const RadioInput = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                {
                    props.options.map((option) => {
                        const checked = props.value == option.value;
                        return (
                            <div className={`${props.inputWrapperClass} ${checked ? props.activeClass : ''}`}>
                                <input
                                    className=  {props.inputClass}
                                    id=         {`${props.idPrefix}-${option.key}`}
                                    key=        {option.keyName}
                                    type=       'radio'
                                    name=       {option.name}
                                    checked=    {checked}
                                    value=      {option.value}
                                    onChange=   {(e) => { props.onChange(e) }}
                                />
                                <label
                                    key={option.key}
                                    htmlFor={`${props.idPrefix}-${option.key}`}
                                    className={props.type === 'radio-select' ? props.radiSelectLabelClass : ''}
                                >
                                    {option.label}
                                    {
                                        props.type === 'radio-color' &&
                                        <p className="color-palette">
                                            {
                                                option.color.map((color) => {
                                                    return <div style={{ backgroundColor: color }} > &nbsp; </div>
                                                })
                                            }
										</p>
                                    }
                                    {
                                        props.type === 'radio-select' &&
                                        <>
                                            <img
                                                src={option.color}
                                                alt={option.label}
                                                className={props.labelImgClass}
									        />
                                            <div className={props.labelOverlayClass}>
                                                {props.labelOverlayText}
                                            </div>
                                        </>
                                    }
                                </label>
                                {
                    props.proSetting && <span className="admin-pro-tag">pro</span>
                }
                            </div>
                        );
                    })
                }
                {
                    props.description &&
                    <p className={props.descClass} dangerouslySetInnerHTML={{__html: props.description}}>
                    </p>
                }
            </div>
        </>
    );
}

export default RadioInput;