<?php

/**
 * Modal Component Class
 *
 * Provides reusable, componentized modal generation with consistent styling,
 * ARIA accessibility attributes, and security features.
 *
 * @package Panel
 * @author Chamalead
 * @version 2.0.0
 */
class Modal
{
    /**
     * Default CSS classes for modal styling
     */
    private const BASE_MODAL_CLASSES = 'fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50';
    private const DESKTOP_MODAL_CLASSES = 'modal-desktop rounded-2xl overflow-hidden flex flex-row fade-in';
    private const SIDEBAR_CLASSES = 'modal-sidebar w-64 p-6 flex flex-col';
    private const MAIN_CONTENT_CLASSES = 'modal-main p-6 flex-1';
    private const ACTION_BAR_CLASSES = 'action-bar flex gap-3 mt-6 p-4 -mx-6 -mb-6 rounded-b-2xl';

    /**
     * Icon configuration for different modal types
     */
    private const ICONS = [
        'create' => ['icon' => 'plus', 'color' => 'sunset-gradient', 'text' => 'white'],
        'edit' => ['icon' => 'edit-2', 'color' => 'bg-blue-500', 'text' => 'white'],
        'view' => ['icon' => 'eye', 'color' => 'bg-emerald-500', 'text' => 'white'],
        'delete' => ['icon' => 'alert-triangle', 'color' => 'bg-rose-500/10', 'text' => 'rose-400']
    ];

    /**
     * Create a "Create Instance" modal
     *
     * @param string $id Modal ID
     * @param string $title Modal title
     * @param array $formConfig Form configuration with fields
     * @param array $options Additional options (sidebarContent, actionButtons)
     * @return string HTML markup
     */
    public static function create(string $id, string $title, array $formConfig = [], array $options = []): string
    {
        $iconConfig = self::ICONS['create'];
        $subtitle = $options['subtitle'] ?? 'Nova instância';

        $sidebar = self::buildSidebar($title, $subtitle, $iconConfig, $options['sidebarContent'] ?? null);
        $form = self::buildCreateForm($formConfig);
        $actions = self::buildActions('create', $options['actionButtons'] ?? null);

        return self::renderModal($id, $title, $sidebar, $form, $actions, 'create');
    }

    /**
     * Create an "Edit Instance" modal
     *
     * @param string $id Modal ID
     * @param string $title Modal title
     * @param string $instanceName Name of instance being edited
     * @param array $formConfig Form configuration
     * @param array $options Additional options
     * @return string HTML markup
     */
    public static function edit(string $id, string $title, string $instanceName, array $formConfig = [], array $options = []): string
    {
        $iconConfig = self::ICONS['edit'];
        $subtitle = $options['subtitle'] ?? 'Configurações';

        $sidebar = self::buildSidebar($title, $subtitle, $iconConfig, null, $instanceName);
        $form = self::buildEditForm($instanceName, $formConfig);
        $actions = self::buildActions('edit', $options['actionButtons'] ?? null);

        return self::renderModal($id, $title, $sidebar, $form, $actions, 'edit');
    }

    /**
     * Create a "View Instance" modal
     *
     * @param string $id Modal ID
     * @param string $title Modal title
     * @param array $options Additional options (headerIcon, contentCallback)
     * @return string HTML markup
     */
    public static function view(string $id, string $title, array $options = []): string
    {
        $iconConfig = $options['headerIcon'] ?? self::ICONS['view'];
        $subtitle = $options['subtitle'] ?? 'Informações da instância';

        $header = self::buildViewHeader($title, $subtitle, $iconConfig);
        $content = self::buildViewContent();
        $footer = self::buildViewFooter();

        return self::renderViewModal($id, $title, $header, $content, $footer);
    }

    /**
     * Create a "Delete Instance" modal
     *
     * @param string $id Modal ID
     * @param string $title Modal title
     * @param string $instanceName Name of instance being deleted
     * @param array $options Additional options
     * @return string HTML markup
     */
    public static function delete(string $id, string $title, string $instanceName, array $options = []): string
    {
        $warning = $options['warning'] ?? 'Esta ação não pode ser desfeita.';
        $confirmText = $options['confirmText'] ?? 'Deletar';
        $cancelText = $options['cancelText'] ?? 'Cancelar';

        return self::renderDeleteModal($id, $title, $instanceName, $warning, $confirmText, $cancelText);
    }

    /**
     * Build sidebar for modals
     *
     * @param string $title Modal title
     * @param string $subtitle Modal subtitle
     * @param array $iconConfig Icon configuration
     * @param string|null $customContent Custom sidebar content
     * @param string|null $instanceName Instance name for edit modals
     * @return string HTML markup
     */
    private static function buildSidebar(string $title, string $subtitle, array $iconConfig, ?string $customContent = null, ?string $instanceName = null): string
    {
        $html = '<div class="' . self::SIDEBAR_CLASSES . '">';

        // Header with icon
        $html .= '<div class="flex items-center gap-3 mb-8">';
        $html .= '<div class="w-10 h-10 rounded-xl ' . htmlspecialchars($iconConfig['color']) . ' flex items-center justify-center flex-shrink-0" aria-hidden="true">';
        $html .= '<i data-lucide="' . htmlspecialchars($iconConfig['icon']) . '" class="w-5 h-5 text-' . htmlspecialchars($iconConfig['text']) . '"></i>';
        $html .= '</div>';
        $html .= '<div>';
        $html .= '<h3 class="text-lg font-bold text-white">' . htmlspecialchars($title) . '</h3>';
        $html .= '<p class="text-xs text-gray-500">' . htmlspecialchars($subtitle) . '</p>';
        $html .= '</div>';
        $html .= '</div>';

        // Instance name display for edit modals
        if ($instanceName !== null) {
            $html .= '<div class="p-3 bg-white/5 rounded-xl border border-white/10 mb-auto">';
            $html .= '<p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Instância</p>';
            $html .= '<p class="text-sm font-semibold text-white truncate" id="editInstanceDisplay">' . htmlspecialchars($instanceName) . '</p>';
            $html .= '</div>';
        }

        // Custom content
        if ($customContent !== null) {
            $html .= $customContent;
        }

        // Close button
        $html .= '<button ';
        $html .= 'onclick="closeModal(\'' . htmlspecialchars($id ?? 'modal') . '\')" ';
        $html .= 'class="mt-auto w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-all"';
        $html .= ' aria-label="Fechar modal" type="button">';
        $html .= '<i data-lucide="x" class="w-4 h-4" aria-hidden="true"></i>';
        $html .= '</button>';

        $html .= '</div>';

        return $html;
    }

    /**
     * Build create form with all standard fields
     *
     * @param array $config Form configuration overrides
     * @return string HTML markup
     */
    private static function buildCreateForm(array $config = []): string
    {
        $instanceNameLabel = $config['instanceNameLabel'] ?? 'Nome da Instância *';
        $instanceNamePlaceholder = $config['instanceNamePlaceholder'] ?? 'Ex: minha-instancia';

        $html = '<form id="createForm" onsubmit="handleCreate(event)">';
        $html .= '<div class="space-y-5">';

        // Instance Name Field
        $html .= '<div class="premium-card">';
        $html .= '<label class="section-title" for="createInstanceName">';
        $html .= '<i data-lucide="tag" class="w-4 h-4" aria-hidden="true"></i>';
        $html .= htmlspecialchars($instanceNameLabel);
        $html .= '</label>';
        $html .= '<input ';
        $html .= 'type="text" ';
        $html .= 'id="createInstanceName" ';
        $html .= 'name="instanceName" ';
        $html .= 'required ';
        $html .= 'minlength="3" ';
        $html .= 'maxlength="50" ';
        $html .= 'class="premium-input" ';
        $html .= 'placeholder="' . htmlspecialchars($instanceNamePlaceholder) . '" ';
        $html .= 'aria-required="true">';
        $html .= '</div>';

        // Configurations
        $html .= self::buildConfigurationSection('create', [
            ['name' => 'readMessages', 'label' => 'Ler Mensagens', 'checked' => true],
            ['name' => 'readStatus', 'label' => 'Ver Status', 'checked' => true],
            ['name' => 'syncFullHistory', 'label' => 'Sincronizar Histórico', 'checked' => true],
            ['name' => 'alwaysOnline', 'label' => 'Sempre Online', 'checked' => false, 'id' => 'createAlwaysOnline'],
            ['name' => 'groupsIgnore', 'label' => 'Ignorar Grupos', 'checked' => false],
            ['name' => 'rejectCall', 'label' => 'Recusar Chamadas', 'checked' => false, 'id' => 'createRejectCall', 'onchange' => 'toggleCreateMsgCall()']
        ]);

        // Reject Call Message (hidden by default)
        $html .= '<div id="createMsgCallContainer" class="hidden">';
        $html .= '<div class="premium-card">';
        $html .= '<label class="section-title" for="createMsgCall">';
        $html .= '<i data-lucide="message-circle" class="w-4 h-4" aria-hidden="true"></i>';
        $html .= 'Mensagem de Recusa';
        $html .= '</label>';
        $html .= '<input ';
        $html .= 'type="text" ';
        $html .= 'id="createMsgCall" ';
        $html .= 'name="msgCall" ';
        $html .= 'class="premium-input" ';
        $html .= 'placeholder="Ex: Estou ocupado no momento, ligo mais tarde.">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>'; // Close space-y-5
        $html .= '</form>';

        return $html;
    }

    /**
     * Build edit form with all standard fields
     *
     * @param string $instanceName Name of instance
     * @param array $config Form configuration
     * @return string HTML markup
     */
    private static function buildEditForm(string $instanceName, array $config = []): string
    {
        $html = '<form id="editForm" onsubmit="handleEdit(event)">';
        $html .= '<input type="hidden" name="instanceName" id="editInstanceName" value="' . htmlspecialchars($instanceName) . '">';

        $html .= '<div class="space-y-5">';

        // Instance Name Display
        $html .= '<div class="premium-card">';
        $html .= '<h4 class="section-title">';
        $html .= '<i data-lucide="tag" class="w-4 h-4" aria-hidden="true"></i>';
        $html .= 'Nome da Instância';
        $html .= '</h4>';
        $html .= '<div class="flex items-center gap-3 p-3 bg-white/5 rounded-xl border border-white/10">';
        $html .= '<div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center flex-shrink-0" aria-hidden="true">';
        $html .= '<i data-lucide="smartphone" class="w-5 h-5 text-blue-400"></i>';
        $html .= '</div>';
        $html .= '<div class="flex-1 min-w-0">';
        $html .= '<p class="text-xs text-gray-500 uppercase tracking-wider">Nome atual</p>';
        $html .= '<p class="text-base font-semibold text-white truncate" id="editInstanceNameDisplay">' . htmlspecialchars($instanceName) . '</p>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<p class="text-xs text-gray-500 mt-2 flex items-center gap-1">';
        $html .= '<i data-lucide="info" class="w-3 h-3" aria-hidden="true"></i>';
        $html .= 'O nome da instância não pode ser alterado após a criação';
        $html .= '</p>';
        $html .= '</div>';

        // Configurations
        $html .= self::buildConfigurationSection('edit', [
            ['name' => 'readMessages', 'label' => 'Ler Mensagens', 'id' => 'editReadMessages'],
            ['name' => 'readStatus', 'label' => 'Ver Status', 'id' => 'editReadStatus'],
            ['name' => 'syncFullHistory', 'label' => 'Sincronizar Histórico', 'id' => 'editSyncFullHistory'],
            ['name' => 'alwaysOnline', 'label' => 'Sempre Online', 'id' => 'editAlwaysOnline'],
            ['name' => 'groupsIgnore', 'label' => 'Ignorar Grupos', 'id' => 'editGroupsIgnore'],
            ['name' => 'rejectCall', 'label' => 'Recusar Chamadas', 'id' => 'editRejectCall', 'onchange' => 'toggleEditMsgCall()']
        ]);

        // Reject Call Message
        $html .= '<div id="editMsgCallContainer" class="hidden">';
        $html .= '<div class="premium-card">';
        $html .= '<label class="section-title" for="editMsgCall">';
        $html .= '<i data-lucide="message-circle" class="w-4 h-4" aria-hidden="true"></i>';
        $html .= 'Mensagem de Recusa';
        $html .= '</label>';
        $html .= '<input ';
        $html .= 'type="text" ';
        $html .= 'name="msgCall" ';
        $html .= 'id="editMsgCall" ';
        $html .= 'class="premium-input" ';
        $html .= 'placeholder="Ex: Estou ocupado no momento, ligo mais tarde.">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>'; // Close space-y-5
        $html .= '</form>';

        return $html;
    }

    /**
     * Build configuration checkboxes section
     *
     * @param string $prefix ID prefix (create/edit)
     * @param array $checkboxes Checkbox configurations
     * @return string HTML markup
     */
    private static function buildConfigurationSection(string $prefix, array $checkboxes): string
    {
        $html = '<div class="premium-card">';
        $html .= '<h4 class="section-title">';
        $html .= '<i data-lucide="settings-2" class="w-4 h-4" aria-hidden="true"></i>';
        $html .= 'Configurações';
        $html .= '</h4>';

        $html .= '<div class="config-grid">';

        foreach ($checkboxes as $checkbox) {
            $id = $checkbox['id'] ?? $prefix . ucfirst($checkbox['name']);
            $checked = isset($checkbox['checked']) && $checkbox['checked'] ? ' checked' : '';
            $onchange = isset($checkbox['onchange']) ? ' onchange="' . htmlspecialchars($checkbox['onchange']) . '"' : '';

            $html .= '<label class="checkbox-premium">';
            $html .= '<input type="checkbox" name="' . htmlspecialchars($checkbox['name']) . '" id="' . htmlspecialchars($id) . '" class="hidden"' . $checked . $onchange . '>';
            $html .= '<div class="checkbox-box" aria-hidden="true">';
            $html .= '<i data-lucide="check" class="w-3.5 h-3.5"></i>';
            $html .= '</div>';
            $html .= '<span class="text-sm text-gray-300">' . htmlspecialchars($checkbox['label']) . '</span>';
            $html .= '</label>';
        }

        $html .= '</div>'; // Close config-grid
        $html .= '</div>';

        return $html;
    }

    /**
     * Build action buttons bar
     *
     * @param string $type Modal type (create/edit/view/delete)
     * @param array|null $customButtons Custom button configuration
     * @return string HTML markup
     */
    private static function buildActions(string $type, ?array $customButtons = null): string
    {
        if ($customButtons !== null) {
            return self::renderCustomActions($customButtons);
        }

        $html = '<div class="' . self::ACTION_BAR_CLASSES . '">';

        // Cancel button
        $html .= '<button ';
        $html .= 'type="button" ';
        $html .= 'onclick="closeModal(\'' . $type . 'Modal\')" ';
        $html .= 'class="btn-premium btn-premium-secondary flex-1 justify-center">';
        $html .= 'Cancelar';
        $html .= '</button>';

        // Submit button
        $submitLabel = $type === 'create' ? 'Criar Instância' : 'Salvar Alterações';
        $submitStyle = $type === 'edit' ? ' style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); box-shadow: 0 4px 15px -2px rgba(59, 130, 246, 0.3);"' : '';
        $submitAria = $type === 'create' ? 'Criar nova instância' : 'Salvar alterações';

        $html .= '<button ';
        $html .= 'type="submit" ';
        $html .= 'form="' . $type . 'Form" ';
        $html .= 'class="btn-premium btn-premium-primary flex-1 justify-center"';
        $html .= $submitStyle;
        $html .= ' aria-label="' . htmlspecialchars($submitAria) . '">';
        $html .= '<span id="' . $type . 'BtnText">' . htmlspecialchars($submitLabel) . '</span>';
        $html .= '<div id="' . $type . 'Spinner" class="hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" aria-hidden="true"></div>';
        $html .= '</button>';

        $html .= '</div>';

        return $html;
    }

    /**
     * Build view modal header
     *
     * @param string $title Modal title
     * @param string $subtitle Modal subtitle
     * @param array $iconConfig Icon configuration
     * @return string HTML markup
     */
    private static function buildViewHeader(string $title, string $subtitle, array $iconConfig): string
    {
        $html = '<div class="flex items-center justify-between p-6 border-b border-white/5">';
        $html .= '<div class="flex items-center gap-3">';
        $html .= '<div class="w-10 h-10 rounded-xl ' . htmlspecialchars($iconConfig['color']) . ' flex items-center justify-center" aria-hidden="true">';
        $html .= '<i data-lucide="' . htmlspecialchars($iconConfig['icon']) . '" class="w-5 h-5 text-' . htmlspecialchars($iconConfig['text']) . '"></i>';
        $html .= '</div>';
        $html .= '<div>';
        $html .= '<h3 id="viewModalTitle" class="text-lg font-bold text-white">' . htmlspecialchars($title) . '</h3>';
        $html .= '<p class="text-xs text-gray-500">' . htmlspecialchars($subtitle) . '</p>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<button ';
        $html .= 'onclick="closeModal(\'viewModal\')" ';
        $html .= 'class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-all"';
        $html .= ' aria-label="Fechar modal" type="button">';
        $html .= '<i data-lucide="x" class="w-4 h-4" aria-hidden="true"></i>';
        $html .= '</button>';

        $html .= '</div>';

        return $html;
    }

    /**
     * Build view modal content area with skeleton loader
     *
     * @return string HTML markup
     */
    private static function buildViewContent(): string
    {
        $html = '<div id="viewContent" class="modal-main p-6">';

        // Skeleton loading state
        $html .= '<div class="space-y-5">';
        $html .= '<div class="flex justify-center">';
        $html .= '<div class="skeleton w-32 h-8 rounded-full"></div>';
        $html .= '</div>';
        $html .= '<div class="grid grid-cols-3 gap-4">';
        $html .= '<div class="skeleton h-20 rounded-xl"></div>';
        $html .= '<div class="skeleton h-20 rounded-xl"></div>';
        $html .= '<div class="skeleton h-20 rounded-xl"></div>';
        $html .= '</div>';
        $html .= '<div class="skeleton h-40 rounded-xl"></div>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    /**
     * Build view modal footer
     *
     * @return string HTML markup
     */
    private static function buildViewFooter(): string
    {
        $html = '<div class="action-bar p-4 flex justify-end">';
        $html .= '<button ';
        $html .= 'onclick="closeModal(\'viewModal\')" ';
        $html .= 'class="btn-premium btn-premium-secondary">';
        $html .= 'Fechar';
        $html .= '</button>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render standard modal (create/edit)
     *
     * @param string $id Modal ID
     * @param string $title Modal title for ARIA
     * @param string $sidebar Sidebar HTML
     * @param string $form Form HTML
     * @param string $actions Actions HTML
     * @param string $type Modal type
     * @return string Complete modal HTML
     */
    private static function renderModal(string $id, string $title, string $sidebar, string $form, string $actions, string $type): string
    {
        $html = '<div ';
        $html .= 'id="' . htmlspecialchars($id) . '" ';
        $html .= 'class="' . self::BASE_MODAL_CLASSES . '" ';
        $html .= 'role="dialog" ';
        $html .= 'aria-modal="true" ';
        $html .= 'aria-labelledby="' . htmlspecialchars($type) . 'ModalTitle">';

        $html .= '<div class="' . self::DESKTOP_MODAL_CLASSES . '">';

        // Sidebar
        $html .= $sidebar;

        // Main content
        $html .= '<div class="' . self::MAIN_CONTENT_CLASSES . '">';
        $html .= $form;
        $html .= $actions;
        $html .= '</div>';

        $html .= '</div>'; // Close modal-desktop
        $html .= '</div>'; // Close modal

        return $html;
    }

    /**
     * Render view modal (different layout)
     *
     * @param string $id Modal ID
     * @param string $title Modal title
     * @param string $header Header HTML
     * @param string $content Content HTML
     * @param string $footer Footer HTML
     * @return string Complete modal HTML
     */
    private static function renderViewModal(string $id, string $title, string $header, string $content, string $footer): string
    {
        $html = '<div ';
        $html .= 'id="' . htmlspecialchars($id) . '" ';
        $html .= 'class="' . self::BASE_MODAL_CLASSES . '" ';
        $html .= 'role="dialog" ';
        $html .= 'aria-modal="true" ';
        $html .= 'aria-labelledby="viewModalTitle">';

        $html .= '<div class="modal-desktop rounded-2xl overflow-hidden flex flex-col fade-in">';

        $html .= $header;
        $html .= $content;
        $html .= $footer;

        $html .= '</div>'; // Close modal-desktop
        $html .= '</div>'; // Close modal

        return $html;
    }

    /**
     * Render delete modal (special compact layout)
     *
     * @param string $id Modal ID
     * @param string $title Modal title
     * @param string $instanceName Instance to delete
     * @param string $warning Warning message
     * @param string $confirmText Confirm button text
     * @param string $cancelText Cancel button text
     * @return string Complete modal HTML
     */
    private static function renderDeleteModal(string $id, string $title, string $instanceName, string $warning, string $confirmText, string $cancelText): string
    {
        $html = '<div ';
        $html .= 'id="' . htmlspecialchars($id) . '" ';
        $html .= 'class="' . self::BASE_MODAL_CLASSES . '" ';
        $html .= 'role="dialog" ';
        $html .= 'aria-modal="true" ';
        $html .= 'aria-labelledby="deleteModalTitle">';

        $html .= '<div class="rounded-2xl overflow-hidden flex flex-row fade-in" style="max-width: 500px; background: rgba(9, 9, 11, 0.98); backdrop-filter: blur(30px); border: 1px solid rgba(244, 63, 94, 0.2); box-shadow: 0 25px 50px -12px rgba(244, 63, 94, 0.15);">';

        // Left side - Icon
        $html .= '<div class="w-32 flex flex-col items-center justify-center p-6" style="background: linear-gradient(180deg, rgba(244, 63, 94, 0.1) 0%, rgba(244, 63, 94, 0.02) 100%); border-right: 1px solid rgba(244, 63, 94, 0.1);" aria-hidden="true">';
        $html .= '<div class="w-16 h-16 bg-rose-500/10 rounded-full flex items-center justify-center">';
        $html .= '<i data-lucide="alert-triangle" class="w-8 h-8 text-rose-400"></i>';
        $html .= '</div>';
        $html .= '</div>';

        // Right side - Content
        $html .= '<div class="flex-1 p-6 flex flex-col">';

        $html .= '<div class="flex justify-between items-start mb-4">';
        $html .= '<div>';
        $html .= '<h3 id="deleteModalTitle" class="text-lg font-bold text-white">' . htmlspecialchars($title) . '</h3>';
        $html .= '<p class="text-rose-400 text-xs mt-1">' . htmlspecialchars($warning) . '</p>';
        $html .= '</div>';

        $html .= '<button ';
        $html .= 'onclick="closeModal(\'deleteModal\')" ';
        $html .= 'class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-all"';
        $html .= ' aria-label="Fechar modal" type="button">';
        $html .= '<i data-lucide="x" class="w-4 h-4" aria-hidden="true"></i>';
        $html .= '</button>';
        $html .= '</div>';

        $html .= '<div class="premium-card mb-6">';
        $html .= '<p class="text-sm text-gray-400">Tem certeza que deseja deletar:</p>';
        $html .= '<p class="text-lg font-semibold text-white mt-1" id="deleteInstanceName">' . htmlspecialchars($instanceName) . '</p>';
        $html .= '</div>';

        $html .= '<div class="flex gap-3 mt-auto">';
        $html .= '<button ';
        $html .= 'onclick="closeModal(\'deleteModal\')" ';
        $html .= 'class="btn-premium btn-premium-secondary flex-1 justify-center">';
        $html .= htmlspecialchars($cancelText);
        $html .= '</button>';

        $html .= '<button ';
        $html .= 'onclick="handleDelete()" ';
        $html .= 'class="btn-premium flex-1 justify-center" ';
        $html .= 'style="background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); color: white; box-shadow: 0 4px 15px -2px rgba(244, 63, 94, 0.3);" ';
        $html .= 'aria-label="Confirmar deleção">';
        $html .= '<span id="deleteBtnText">' . htmlspecialchars($confirmText) . '</span>';
        $html .= '<div id="deleteSpinner" class="hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" aria-hidden="true"></div>';
        $html .= '</button>';
        $html .= '</div>';

        $html .= '</div>'; // Close content
        $html .= '</div>'; // Close modal-inner
        $html .= '</div>'; // Close modal

        return $html;
    }

    /**
     * Render custom action buttons
     *
     * @param array $buttons Button configurations
     * @return string HTML markup
     */
    private static function renderCustomActions(array $buttons): string
    {
        $html = '<div class="' . self::ACTION_BAR_CLASSES . '">';

        foreach ($buttons as $button) {
            $type = $button['type'] ?? 'button';
            $onclick = isset($button['onclick']) ? ' onclick="' . htmlspecialchars($button['onclick']) . '"' : '';
            $class = $button['class'] ?? 'btn-premium btn-premium-secondary';
            $label = htmlspecialchars($button['label'] ?? 'Button');
            $style = isset($button['style']) ? ' style="' . htmlspecialchars($button['style']) . '"' : '';
            $aria = isset($button['aria']) ? ' aria-label="' . htmlspecialchars($button['aria']) . '"' : '';

            $html .= '<button ';
            $html .= 'type="' . $type . '"';
            $html .= $onclick;
            $html .= ' class="' . htmlspecialchars($class) . '"';
            $html .= $style;
            $html .= $aria;
            $html .= '>';
            $html .= $label;

            if (isset($button['spinner'])) {
                $html .= '<div id="' . htmlspecialchars($button['spinner']) . '" class="hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" aria-hidden="true"></div>';
            }

            $html .= '</button>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Generate a generic modal with custom content
     *
     * @param string $id Modal ID
     * @param string $title Modal title
     * @param string $content HTML content
     * @param array $options Additional options
     * @return string HTML markup
     */
    public static function custom(string $id, string $title, string $content, array $options = []): string
    {
        $size = $options['size'] ?? 'medium'; // small, medium, large, full
        $icon = $options['icon'] ?? 'info';
        $iconColor = $options['iconColor'] ?? 'bg-gray-500';

        $sizeClasses = [
            'small' => 'max-w-md',
            'medium' => 'max-w-2xl',
            'large' => 'max-w-4xl',
            'full' => 'max-w-full mx-4'
        ];

        $html = '<div ';
        $html .= 'id="' . htmlspecialchars($id) . '" ';
        $html .= 'class="' . self::BASE_MODAL_CLASSES . '" ';
        $html .= 'role="dialog" ';
        $html .= 'aria-modal="true" ';
        $html .= 'aria-labelledby="' . htmlspecialchars($id) . 'Title">';

        $html .= '<div class="' . ($sizeClasses[$size] ?? $sizeClasses['medium']) . ' rounded-2xl overflow-hidden flex flex-col fade-in bg-[rgba(9,9,11,0.98)] border border-white/10">';

        // Header
        if (!isset($options['hideHeader']) || !$options['hideHeader']) {
            $html .= '<div class="flex items-center justify-between p-6 border-b border-white/5">';
            $html .= '<div class="flex items-center gap-3">';
            $html .= '<div class="w-10 h-10 rounded-xl ' . htmlspecialchars($iconColor) . ' flex items-center justify-center" aria-hidden="true">';
            $html .= '<i data-lucide="' . htmlspecialchars($icon) . '" class="w-5 h-5 text-white"></i>';
            $html .= '</div>';
            $html .= '<h3 id="' . htmlspecialchars($id) . 'Title" class="text-lg font-bold text-white">' . htmlspecialchars($title) . '</h3>';
            $html .= '</div>';

            $html .= '<button ';
            $html .= 'onclick="closeModal(\'' . htmlspecialchars($id) . '\')" ';
            $html .= 'class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-all"';
            $html .= ' aria-label="Fechar modal" type="button">';
            $html .= '<i data-lucide="x" class="w-4 h-4" aria-hidden="true"></i>';
            $html .= '</button>';
            $html .= '</div>';
        }

        // Content
        $html .= '<div class="modal-main p-6">';
        $html .= $content;
        $html .= '</div>';

        // Footer (optional)
        if (isset($options['footer'])) {
            $html .= '<div class="action-bar p-4 flex justify-end">';
            $html .= $options['footer'];
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
