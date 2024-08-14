const Recaptcha = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                {props.script}
                <input
                    type="hidden"
                    name={`${props.key}-value`}
                    value="Verified"
                />
                <input
                    type="hidden"
                    name={`${props.key}-label`}
                    value={props.label}
                />
                <input
                    type="hidden"
                    name={`${props.key}-type`}
                    value="recaptcha"
                />
                {
                    props.recaptchaType === 'v3' &&
                    <div>
                        <input
                            type="hidden"
                            name="recaptchav3Response"
                            id="recaptchav3Response"
                        />
                        <input
                            type="hidden"
                            name="recaptchav3_sitekey"
                            value={props.sitekey}
                        />
                        <input
                            type="hidden"
                            name="recaptchav3_secretkey"
                            value={props.secretkey}
                        />
                    </div>
                }
                <input
                    type="hidden"
                    name="g-recaptchatype"
                    value={props.recaptchaType}
                />
            </div>
        </>
    );
}

export default Recaptcha;