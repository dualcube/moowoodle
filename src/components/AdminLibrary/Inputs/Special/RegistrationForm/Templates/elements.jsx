import { __ } from "@wordpress/i18n";

const Elements = ( props ) => {
    const { selectOptions, onClick } = props;

    return (
        <>
            <aside className='elements-section'>
                <div className='section-meta'>
                    <h2>{__("Form fields", "catalogx")}</h2>
                </div>
                <main className='section-container'>
                    {
                        selectOptions.map((option) => (
                            <article
                                className='elements-items'
                                onClick={(event) => onClick(option.value)}
                            >
                                <i className={` ${option.icon}`}></i>
                                <p className='list-title'>{option.label}</p>
                            </article>
                        ))
                    }
                </main>
            </aside>
        </>
    )
  }
  export default Elements