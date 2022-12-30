# ai-chatbot-wordpress
a wordpress plugin that allows customers to talk to the openai chatbot

This plugin creates a shortcode that displays a form for the user to enter their question, and a button to submit the form. 
When the form is submitted, the plugin sends the question to the OpenAI chat model using the `openai_chat_model_response()` function, which makes a request to the OpenAI API and returns the response. The response is then displayed to the user in the `#chatbot-response` element.
