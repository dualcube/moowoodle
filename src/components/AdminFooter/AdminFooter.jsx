import './AdminFooter.scss';

const AdminFooter = () => {

    const supportLink = [
      {
        title: "Get in touch with Support",
        icon: "mail",
        description: "Reach out to the support team for assistance or guidance.",
        link: "https://dualcube.com/forums/?utm_source=wordpress.org&utm_medium=freelandingpage&utm_campaign=MooWoodleFree",
      },
      {
        title: "Explore Documentation",
        icon: "submission-message",
        description: "Understand the plugin and its settings.",
        link: "https://dualcube.com/knowledgebase/?utm_source=wordpress.org&utm_medium=freelandingpage&utm_campaign=MooWoodleFree",
      },
      {
        title: "Contribute Here",
        icon: "support",
        description: "To participation in product enhancement.",
        link: "https://github.com/dualcube/moowoodle/issues",
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
