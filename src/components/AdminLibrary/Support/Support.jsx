import React, { useState, useEffect } from "react";
import "./support.scss";

const Support = () => {
  const url = "https://www.youtube.com/embed/fL7wPVYopTU?si=BZeP1WwCxBSSoM7h";

  const [faqs, setFaqs] = useState([
    {
      question:
        "How do I resolve a timeout error when WordPress connects with Moodle?",
      answer:
        "When encountering a timeout error during WordPress-Moodle communication, adjust timeout settings in your server configuration to accommodate longer communication durations.",
      open: true,
    },
    {
      question: "How can I troubleshoot connection errors during Test connection?",
      answer:
        "Navigate to the \"Log\" menu, where you can use the \"Log\" feature to troubleshoot connectivity issues between your store and Moodle. This tool helps identify necessary changes for resolution.",
      open: false,
    },
    {
      question:
        "Why aren't my customers receiving enrollment emails?",
      answer:
        "Install a plugin like Email Log to check if New Enrollment emails are logged. If logged, your email functionality is working fine; if not, contact your email server administrator for assistance.",
      open: false,
    },
    {
      question: "Can I set course expiration dates using MooWoodle?",
      answer:
        'Course-related functionalities, including setting expiration dates, are managed within Moodle itself; MooWoodle does not control these aspects.',
      open: false,
    },
  ]);

  const toggleFAQ = (index) => {
    setFaqs(
      faqs.map((faq, i) => {
        if (i === index) {
          faq.open = !faq.open;
        } else {
          faq.open = false;
        }

        return faq;
      })
    );
  };

  return (
    <>
      <div className="dynamic-fields-wrapper">
        <div className="support-container">
          <div className="support-header-wrapper">
            <h1 className="support-heading">
              Thank you for using MooWoodle
            </h1>
            <p className="support-subheading">
              We want to help you enjoy a wonderful experience with all of our
              products.
            </p>
          </div>
          <div className="video-faq-wrapper">
            <div className="video-section">
              <iframe
                src={url}
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
              />
            </div>
            <div className="faq-section">
              <div className="faqs">
                {faqs.map((faq, index) => (
                  <div
                    className={"faq " + (faq.open ? "open" : "")}
                    key={index}
                    onClick={() => toggleFAQ(index)}
                  >
                    <div className="faq-question">{faq.question}</div>
                    <div className="faq-answer" dangerouslySetInnerHTML={{__html: faq.answer}}></div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default Support;
