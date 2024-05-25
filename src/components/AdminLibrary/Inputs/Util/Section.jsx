const Section = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                &nbsp;
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