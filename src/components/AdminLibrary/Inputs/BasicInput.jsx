const BasicInput = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                <input
                    className=  {props.inputClass}
                    id=         {props.id}
                    key=        {props.key}
                    type=       {props.type || 'text'}
                    name=       {props.name || 'basic-input'}
                    value=      {props.value}
                    placeholder={props.placeholder}
                    onChange=   {(e) => { props.onChange?.(e) }}
                    onClick=    {(e) => { props.onClick?.(e) }}
                    onMouseOver={(e) => { props.onMouseOver?.(e) }}
                    onMouseOut= {(e) => { props.onMouseOut?.(e) }}
                    onFocus=    {(e) => { props.onFocus?.(e) }}
                />
                {
                    props.proSetting && <span className="admin-pro-tag">pro</span>
                }
                {
                    props.description &&
                    <p className={props.descClass} dangerouslySetInnerHTML={{ __html: props.description }} >
                    </p>
                }
            </div>
        </>
    );
}

export default BasicInput;