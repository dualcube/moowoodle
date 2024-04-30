const ToggleRectangle = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                <div className={props.inputWrapperClass}>
                    <ul>
                        {
                            props.options.map((options) => {
                                return (
                                    <li className={props.inputWrapperClass}>
                                        <input
                                            className=  {props.inputClass}
                                            id=         {`${props.idPrefix}-${options.key}`}
                                            key=        {options.key}
                                            type=       'radio'
                                            name=       {options.name}
                                            checked=    {props.value == options.value}
                                            value=      {options.value}
                                            onChange=   {(e) => { props.onChange(e) }}
                                        />
                                        <label
                                            key={options.key}
                                            htmlFor={`${props.idPrefix}-${options.key}`}
                                        >
                                            {options.label}
                                        </label>
                                        {
                    props.proSetting && <span className="admin-pro-tag">pro</span>
                }
                                    </li>
                                );
                            })
                        }
                    </ul>
                </div>
                
                {
                    props.description &&
                    <p className={props.descClass} dangerouslySetInnerHTML={{__html: props.description}}>
                    </p>
                }
            </div>
        </>
    );
}

export default ToggleRectangle;