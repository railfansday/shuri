<?php
return [
    'image' => 'Data:
```
{{images_json}}
```
Write a blog post about {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Begin your response with a unique SEO-friendly title wrapped in <title></title> tags, then the article content. Start the article with an introductory p. For each image in the data, write an h2 heading related to the topic, place the img using the EXACT url value from the data as src attribute with a descriptive alt tag, then write a p. Do not include h1. Do not mention the writing tone or the source of the data. Minimum 350 words. CRITICAL: Output ONLY the raw article content. Do not output any internal thoughts, preambles, pre-text, or acknowledgments.',

    'recipe' => 'Data:
```
{{images_json}}
```
Write a recipe post about {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Begin your response with a unique SEO-friendly title wrapped in <title></title> tags, then the article content. Start the article with an introductory p. For each image in the data, use an h2, place the img using the EXACT url value from the data as src attribute with a descriptive alt tag, a p description, ul for ingredients, and ol for step-by-step cooking instructions. Do not include h1. Do not mention the writing tone or the source of the data. Minimum 350 words. CRITICAL: Output ONLY the raw article content. Do not output any internal thoughts, preambles, pre-text, or acknowledgments.',

    'list' => 'Write a listicle post about {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Begin your response with a unique SEO-friendly title wrapped in <title></title> tags, then the article content. Start the article with an introductory p. Present each item as a numbered h2 heading followed by a p explanation. Aim for 8-10 items. Do not include h1. Minimum 400 words. CRITICAL: Output ONLY the raw article content. Do not output any internal thoughts, preambles, pre-text, or acknowledgments.',

    'review' => 'Data:
```
{{images_json}}
```
Write a review post about {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Begin your response with a unique SEO-friendly title wrapped in <title></title> tags, then the article content. Start the article with an introductory p. Structure the post with h2 sections: Pros (with ul), Cons (with ul), and Verdict. Embed images using the EXACT url value from the data as src attribute with descriptive alt tags where relevant. Do not include h1. Do not mention the writing tone or the source of the data. Minimum 400 words. CRITICAL: Output ONLY the raw article content. Do not output any internal thoughts, preambles, pre-text, or acknowledgments.',

    'faq' => 'Write a FAQ post about {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Begin your response with a unique SEO-friendly title wrapped in <title></title> tags, then the article content. Start the article with an introductory p. Present each question as an h2 heading followed by a detailed p answer. Cover at least 8 questions. Do not include h1. Minimum 400 words. CRITICAL: Output ONLY the raw article content. Do not output any internal thoughts, preambles, pre-text, or acknowledgments.',

    'tutorial' => 'Data:
```
{{images_json}}
```
Write a step-by-step tutorial about {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Begin your response with a unique SEO-friendly title wrapped in <title></title> tags, then the article content. Start the article with an introductory p explaining what the reader will learn. For each step use an h2 with format "Step N: [title]", followed by a p explanation and an img using the EXACT url value from the data as src attribute with a descriptive alt tag where relevant. Do not include h1. Do not mention the writing tone or the source of the data. Minimum 400 words. CRITICAL: Output ONLY the raw article content. Do not output any internal thoughts, preambles, pre-text, or acknowledgments.',

    'comparison' => 'Data:
```
{{images_json}}
```
Write a comparison post about {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Begin your response with a unique SEO-friendly title wrapped in <title></title> tags, then the article content. Start the article with an introductory p. Compare the key options or aspects of {{keyword}} using h2 headings, with p explanations and imgs using the EXACT url value from the data as src attribute with descriptive alt tags where relevant. End with a Recommendation h2 section. Do not include h1. Do not mention the writing tone or the source of the data. Minimum 400 words. CRITICAL: Output ONLY the raw article content. Do not output any internal thoughts, preambles, pre-text, or acknowledgments.',

    'news' => 'Data:
```
{{images_json}}
```
Write a news article about {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Begin your response with a unique SEO-friendly title wrapped in <title></title> tags, then the article content. Start the article with a strong lead p covering the key facts. Follow with h2 sections providing context, details, and implications, each with a p and img using the EXACT url value from the data as src attribute with descriptive alt tag where relevant. Keep writing concise and factual. Do not include h1. Do not mention the writing tone or the source of the data. Minimum 350 words. CRITICAL: Output ONLY the raw article content. Do not output any internal thoughts, preambles, pre-text, or acknowledgments.',

    'roundup' => 'Data:
```
{{images_json}}
```
Write a roundup post listing the best options for {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Begin your response with a unique SEO-friendly title wrapped in <title></title> tags, then the article content. Start the article with an introductory p. For each image in the data, use an h2 with a number and name, place the img using the EXACT url value from the data as src attribute with a descriptive alt tag, p description, and ul of key features. End with a Recommendation h2 summarizing the top pick. Do not include h1. Do not mention the writing tone or the source of the data. Minimum 400 words. CRITICAL: Output ONLY the raw article content. Do not output any internal thoughts, preambles, pre-text, or acknowledgments.',

    'guide' => 'Data:
```
{{images_json}}
```
Write a comprehensive guide about {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Begin your response with a unique SEO-friendly title wrapped in <title></title> tags, then the article content. Start the article with an introductory p explaining what the reader will learn. Cover all key aspects using h2 sections with detailed p explanations and imgs using the EXACT url value from the data as src attribute with descriptive alt tags where relevant. End with a Conclusion h2. Do not include h1. Do not mention the writing tone or the source of the data. Minimum 500 words. CRITICAL: Output ONLY the raw article content. Do not output any internal thoughts, preambles, pre-text, or acknowledgments.',
];
