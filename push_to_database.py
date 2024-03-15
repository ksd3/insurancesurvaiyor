import os
import sys
import requests

def upload_image_to_github(image_path, client_name, date_of_visit, client_id, github_token, repo_owner, repo_name, commit_message):
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

if __name__ == "__main__":
    # Extract arguments passed from PHP
    image_path = sys.argv[1]
    client_name = sys.argv[2]
    date_of_visit = sys.argv[3]
    client_id = sys.argv[4]
    github_token = sys.argv[5]
    repo_owner = sys.argv[6]
    repo_name = sys.argv[7]
    commit_message = sys.argv[8]

    upload_image_to_github(image_path, client_name, date_of_visit, client_id, github_token, repo_owner, repo_name, commit_message)
