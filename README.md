# File-Scanner-NLP

**Overview**
The Intelligent Document Query System is a web-based application designed to extract information from PDF documents and provide accurate answers to user queries. By leveraging Natural Language Processing (NLP), the system interprets user questions and retrieves relevant information from the uploaded documents.
The project aims to simplify the interaction between users and unstructured data by delivering a seamless, intuitive experience for document-based question answering.

**Features**
**PDF File Upload**: Allows users to upload PDF documents for processing.
**Text Extraction**: Extracts text from uploaded documents using robust libraries like PyMuPDF and PyPDF2.
**Natural Language Query**: Enables users to ask questions in plain English, making the system user-friendly.
**Answer Generation**: Uses NLP models to analyze queries and extract the most relevant information from the document.
**Legend for Query Guidance**: A floating legend on the interface guides users on how to phrase questions effectively.
**Responsive Design**: User interface optimized for usability, with clearly defined sections for uploading files, asking questions, and viewing results.

**Technologies Used**
**Frontend**: HTML, CSS, JavaScript (for a clean and interactive user experience).
**Backend**: Python (for text extraction and NLP processing).
**NLP Models**: SpaCy with the transformer-based en_core_web_trf model for advanced natural language understanding.

**Libraries:**
PyMuPDF and PyPDF2 for PDF text extraction.
SpaCy for NLP processing.
JSON for handling data exchange.

**Installation**
**Prerequisites**
Ensure you have the following installed on your system:
**Python 3.12 or later.
pip (Python package manager).
XAMPP or any local server environment for PHP and file hosting.**

**Steps**
Clone the repository:
git clone https://github.com/your-repo-name/intelligent-document-query-system.git

Navigate to the project directory:
cd intelligent-document-query-system

Install the required Python packages:
pip install -r requirements.txt

Install SpaCy models:
python -m spacy download en_core_web_trf

Start your local server (e.g., XAMPP) and place the project folder in the appropriate directory (e.g., htdocs for XAMPP).
Access the web application by navigating to:
http://localhost/intelligent-document-query-system

**How to Use**

**Upload a PDF**
Navigate to the Upload Files section on the homepage.
Click "Choose File" to upload a PDF.
Once uploaded, the document content will be processed and made available for querying.

**Ask Questions**
Type your question into the Ask a Question input box.
Refer to the Legend (always visible on the top right) for examples of how to phrase your questions effectively.
Submit your query to receive the most relevant answer based on the uploaded PDF.

**Project Goals**

Enable seamless interaction with document-based data through natural language queries.
Leverage advanced NLP techniques to provide accurate, context-aware answers.
Simplify document analysis for academic, business, and personal use cases.
