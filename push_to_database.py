import os
import json
import requests

def upload_to_github(image_path, client_name, date_of_visit, client_id, github_token, repo_owner, repo_name, commit_message):
    # Base URL for GitHub API
    base_url = "https://api.github.com"

    # Set up headers with authentication token
    headers = {
        "Authorization": f"token {github_token}",
        "Accept": "application/vnd.github.v3+json"
    }

    # Read the image file
    with open(image_path, "rb") as image_file:
        # Define file name and file content
        file_name = os.path.basename(image_path)
        file_content = image_file.read()

        # Create file payload for GitHub API
        files = {
            "file": (file_name, file_content)
        }

        # Define data payload for GitHub API
        data = {
            "message": commit_message,
            "content": file_content,
            "branch": "main"  # You can change the branch if needed
        }

        # Make request to create a new file in the repository
        upload_url = f"{base_url}/repos/{repo_owner}/{repo_name}/contents/{file_name}"
        response = requests.put(upload_url, headers=headers, json=data, files=files)

        # Check if upload was successful
        if response.status_code == 201:
            print("Image uploaded successfully.")
        else:
            print(f"Failed to upload image. Status code: {response.status_code}")
            print(response.text)

def push_data_to_github(data_file_path, images_directory, github_token, repo_owner, repo_name):
    # Read data from JSON file
    with open(data_file_path, "r") as json_file:
        data = json.load(json_file)

    # Upload each image along with its data
    for item in data:
        image_path = os.path.join(images_directory, os.path.basename(item["image"]))
        client_name = item["client_name"]
        date_of_visit = item["date_of_visit"]
        client_id = item["client_id"]
        commit_message = f"Add image and data for {client_name}"
        
        # Upload image to GitHub
        upload_to_github(image_path, client_name, date_of_visit, client_id, github_token, repo_owner, repo_name, commit_message)

if __name__ == "__main__":
    # Define paths and GitHub credentials
    data_json_path = "path/to/data.json"
    images_directory = "path/to/images/directory"
    github_token = "YOUR_ACCESS_TOKEN"
    repo_owner = "YOUR_GITHUB_USERNAME"
    repo_name = "YOUR_REPOSITORY_NAME"

    # Push data to GitHub
    push_data_to_github(data_json_path, images_directory, github_token, repo_owner, repo_name)
