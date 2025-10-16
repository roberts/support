{
  "yaml.schemas": {
    "https://json.schemastore.org/github-workflow.json": ".github/workflows/*.yml"
  },
  "yaml.customTags": [
    "!secret scalar",
    "!vault scalar"
  ],
  "github-actions.workflows.pinned.workflows": [
    ".github/workflows/deploy.yml"
  ],
  "files.associations": {
    "*.yml": "yaml"
  },
  "yaml.validate": true,
  "yaml.completion": true,
  "[yaml]": {
    "editor.defaultFormatter": "redhat.vscode-yaml"
  },
  "yaml.schemaStore.enable": true
}
