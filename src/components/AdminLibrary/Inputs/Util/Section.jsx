const Section = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                
                {props.value && (
                    <span>{props.value}</span>
                )}
                {props.hint && (
                    <p className="section-hint" dangerouslySetInnerHTML={{__html: props.hint}}></p>
                )}
            </div>
        </>
    );
}

export default Section;