# OpenAI Translation Model
![OpenAI Logo](https://upload.wikimedia.org/wikipedia/commons/5/57/OpenAI.png)

## Custom Training Setup

When training a custom translation model, having well-structured **context files** is essential. These files streamline the process, helping the model behave more accurately and consistently across different languages.

Training files are just an example of how to train your model. Example training files located in Training Files
## Project Overview

This project is designed to test a custom translation model trained using multilingual sentence pairs. The dataset includes translations in several languages:

- **Portuguese**
- **Chinese**
- **Russian**
- **Arabic**
- **Japanese**
- **Spanish**
- **German**
- **French**

Additionally, **contextual information** is provided within the training files to enhance the model’s understanding and accuracy.

## Results

Results where given by using gpt-3.5 - where all over the place in **Russian**, **Arabic**, **Japanese** and **Chinese** from 30% to 400% correct

**Spanish**, **Portuguese** and **German** where 80% - 100% correct after correction by professional translators

**French** was in the 40% - 70% mark

## Conclusion 

Given the current state of AI - the cost of training a model can go from 1$ to 5$ and given that new models like gpt-4.5 is out now, results might need to be checked again creating a new custom model with this new versions.
## Code Snippet

This repository contains a **testing script** for evaluating the model's performance. The script processes text samples and verifies the model’s translation accuracy across different languages.

### Features

- **Multilingual translation evaluation**
- **Context-aware training data**
- **Optimized for OpenAI's model framework**

## Usage

# Clone the repository
git clone https://github.com/Juan-DP/OpenAITranslationModel.git

# Navigate to the project folder
cd OpenAITranslationModel

# Run the test script
php test_translation


