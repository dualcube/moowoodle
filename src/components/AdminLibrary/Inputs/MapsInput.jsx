const MapsInput = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                <input
                    className=  {props.inputClass}
                    id=         {props.id}
                    key=        {props.key}
                    type=       {props.type || 'text'}
                    name=       {props.name || 'maps'}
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
                <div
                    id=         {props.containerId    || 'maps-container'}
                    className=  {props.containerClass || 'maps-container'}
                    style=      {props.containerStyle || { width: '100%', height: '300px' }}
                ></div>
                {
                    props.description &&
                    <p className={props.descClass} dangerouslySetInnerHTML={{__html: props.description}}>
                        
                    </p>
                }
            </div>
        </>
    );
}

export default MapsInput;