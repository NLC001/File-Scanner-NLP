import sys
import json
import fitz  # PyMuPDF
import logging
import os
from sentence_transformers import SentenceTransformer, util

# Configure logging to write to debug_log.txt
logging.basicConfig(filename="debug_log.txt", level=logging.DEBUG, format="%(asctime)s - %(levelname)s - %(message)s")

# PDF text extraction function
def extract_text_from_pdf(file_path):
    text = ""
    if not os.path.isfile(file_path) or os.path.getsize(file_path) == 0:
        logging.error("File does not exist or is empty.")
        return text
    
    try:
        logging.info(f"Attempting to open file {file_path}")
        with fitz.open(file_path) as doc:
            for page in doc:
                text += page.get_text()
        logging.info("PDF text extraction with PyMuPDF successful.")
    except Exception as e:
        logging.error(f"PyMuPDF failed: {e}")
    return text

# Sentence-based chunking function
def chunk_text_into_sentences(text):
    """Splits text into individual sentences."""
    return text.split(". ")

# NLP processing function using SentenceTransformer
def process_nlp(question, content):
    logging.info("Loading NLP model.")
    model = SentenceTransformer('multi-qa-mpnet-base-dot-v1')
    logging.info("NLP model loaded successfully.")

    # Split content into sentences for more precise matching
    sentences = chunk_text_into_sentences(content)
    question_embedding = model.encode(question, convert_to_tensor=True)
    sentence_embeddings = model.encode(sentences, convert_to_tensor=True)

    # Compute similarities and rank by similarity score
    similarities = util.pytorch_cos_sim(question_embedding, sentence_embeddings)[0]
    sorted_results = sorted(
        [(similarity.item(), sentence) for similarity, sentence in zip(similarities, sentences)],
        key=lambda x: x[0],
        reverse=True
    )

    # Retrieve top 2–3 most relevant sentences with a similarity threshold
    top_matches = [result[1] for result in sorted_results[:3] if result[0] > 0.5]  # Use 0.5 as a similarity threshold

    if top_matches:
        logging.info(f"Best matches based on similarity: {top_matches}")
        return top_matches
    else:
        logging.warning("No relevant answers found above the similarity threshold.")
        return ["No relevant answer found."]

# Main function to run the script
if __name__ == "__main__":
    if len(sys.argv) != 3:
        logging.error("Invalid usage: correct format is 'python nlp_processor.py <question> <content_file_path>'")
        sys.exit(1)
    
    question = sys.argv[1]
    content_file_path = sys.argv[2]
    
    try:
        logging.info(f"Starting process with question: {question} and file: {content_file_path}")
        content = extract_text_from_pdf(content_file_path)
        if content:
            logging.info("File content extracted successfully.")
        else:
            logging.warning("File content extraction failed or returned empty text.")
        
        answers = process_nlp(question, content)
        print(json.dumps(answers))
    except Exception as e:
        logging.error(f"Error during processing: {e}")
        print(json.dumps(["Error processing the content."]))
        sys.exit(1)
