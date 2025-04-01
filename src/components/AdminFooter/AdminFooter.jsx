import './AdminFooter.scss';
import { __ } from '@wordpress/i18n';

const AdminFooter = () => {

    const supportLink = [
        {
          title: __("Get in touch with Support", "catalogx"),
          icon: "mail",
          description: __("Reach out to the support team for assistance or guidance.", "catalogx"),
          link: "https://catalogx.com/support/?utm_source=plugin&utm_medium=settings&utm_campaign=tracking",
        },
        {
          title: __("Explore Documentation", "catalogx"),
          icon: "submission-message",
          description: __("Understand the plugin and its settings.", "catalogx"),
          link: "https://catalogx.com/docs/?utm_source=plugin&utm_medium=settings&utm_campaign=tracking",
        },
        {
          title: __("Contribute Here", "catalogx"),
          icon: "support",
          description: __("To participate in product enhancement.", "catalogx"),
          link: "https://github.com/multivendorx/catalogx/issues?utm_source=plugin&utm_medium=settings&utm_campaign=tracking",
        },
      ];

    return (
        <>
            <div className="support-card">
            {supportLink.map((item, index) => {
                return (
                <>
                    <a href={item.link} target="_blank" className="card-item">
                    <i className={`admin-font adminLib-${item.icon}`}></i>
                    <a href={item.link} target="_blank">
                        {item.title}
                    </a>
                    <p>{item.description}</p>
                    </a>
                </>
                );
            })}
            </div>
        </>
    )
}
export default AdminFooter;
