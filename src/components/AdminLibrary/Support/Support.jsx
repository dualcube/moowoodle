import React, {useState, useEffect} from "react";
import "./support.scss";

const questions = [
  {
    id: 1,
    question: 'Popular Articles',
    answer: 'Suspendisse ipsum elit, hendrerit id eleifend at, condimentum et mauris. Curabitur et libero vel arcu dignissim pulvinar ut ac leo. In sit amet orci et erat accumsan interdum.',
  },
    {
    id: 2,
    question: 'Fix problems & request removals',
    answer: 'Suspendisse ipsum elit, hendrerit id eleifend at, condimentum et mauris. Curabitur et libero vel arcu dignissim pulvinar ut ac leo. In sit amet orci et erat accumsan interdum.',
  },
    {
    id: 3,
    question: 'Browse the web',
    answer: 'Suspendisse ipsum elit, hendrerit id eleifend at, condimentum et mauris. Curabitur et libero vel arcu dignissim pulvinar ut ac leo. In sit amet orci et erat accumsan interdum.',
  },
      {
    id: 4,
    question: 'Search on your phone or tablet',
    answer: 'Suspendisse ipsum elit, hendrerit id eleifend at, condimentum et mauris. Curabitur et libero vel arcu dignissim pulvinar ut ac leo. In sit amet orci et erat accumsan interdum.',
  },
  
]

function FAQ(props) {    
    const [searchTerm, setSearchTerm] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const handleSearchChange = e => {
      setSearchTerm(e.target.value);
    };
    
    useEffect(() => {
      const results = props.data.filter(item=>
        item.question.toLowerCase().includes(searchTerm)
      );
      setSearchResults(results);
    }, [searchTerm]);
    
    return (    
      <div className='container'>
        <h2 className="heading">How can we help you?</h2>
        <section className='faq'>
         {searchResults.map(item => <Question question={item.question} answer={item.answer} />)}
        </section>      
      </div>
    )
  }

  const Question = props => {
    const [isActive, setActive] = React.useState(false);
    const handleClick = (id) => {
     setActive(!isActive)
   }
     return(
       <div className="question-wrapper">
       <div className='question' id={props.id}>
         <h3>{props.question}</h3>
         <button onClick={() => handleClick(props.id)}>
            <svg className={isActive? 'active' : ''} viewBox="0 0 320 512" width="100" title="angle-down">
              <path d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z" />
            </svg>
         </button>     
       </div>
       <div className={isActive? 'answer active' : 'answer'}>{props.answer}</div>
       </div>
     )
   }

const Support = () => {
  const url = "https://www.youtube.com/embed/fL7wPVYopTU?si=zS1TSj-YU-yx2Nr9";

  const supportLink = [
    {
      title: "Get in Touch with Support",
      icon: "mail",
      description: "Reach out to the support team for assistance or guidance.",
      link: "link1",
    },
    {
      title: "Explore Documentation",
      icon: "submission-message",
      description: "Understand the plugin and its settings.",
      link: "https://multivendorx.com/docs/knowledgebase/products-stock-manager-notifier-for-woocommerce/",
    },
    {
      title: "Contribute Here",
      icon: "support",
      description: "To participation in product enhancement.",
      link: "link3",
    },
  ];


  return (
    <>
      <div className="dynamic-fields-wrapper">
        <div className="support-container">
          <div className="support-header-wrapper">
            <h1 className="support-heading">
              Thank you for using Product Stock Manager & Notifier for
              WooCommerce
            </h1>
            <p className="support-subheading">
              We want to help you enjoy a wonderful experience with all of our
              products.
            </p>
          </div>
          <div className="support-container-wrapper">
            <div className="video-support-wrapper">
              <iframe
                src={url}
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
              />
            </div>
            <div className="support-quick-link">
              {supportLink?.map((item, index) => {
                return (
                  <>
                    <div key={index} className="support-quick-link-items">
                      <div className="icon-bar">
                        <i className={`admin-font font-${item.icon}`}></i>
                      </div>
                      <div className="content">
                        <a href={item.link} target="_blank">{item.title}</a>
                        <p>{item.description}</p>
                      </div>
                    </div>
                  </>
                );
              })}
            </div>
          </div>
          <div className="faq-wrapper">
          <FAQ data={questions}/>
          </div>
        </div>
      </div>
    </>
  );
};

export default Support;
