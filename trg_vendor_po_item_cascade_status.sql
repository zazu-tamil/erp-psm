-- =====================================================================================
-- Trigger: trg_vendor_po_item_after_update_cascade_status
-- Purpose: When a vendor PO item (vendor_po_item_info) status is set to 'Deleted' or 'Delete',
--          automatically cascade the status, updated_by, and updated_date to:
--            1. vendor_purchase_invoice_item_info
--            2. vendor_purchase_multiple_invoice_item_info
--            3. vendor_pur_inward_item_info
-- =====================================================================================

DELIMITER $$

DROP TRIGGER IF EXISTS `trg_vendor_po_item_after_update_cascade_status`$$

CREATE TRIGGER `trg_vendor_po_item_after_update_cascade_status`
AFTER UPDATE ON `vendor_po_item_info`
FOR EACH ROW
BEGIN
    -- Check if status transitioned to a deleted state ('Deleted' or 'Delete')
    IF (NEW.status IN ('Deleted', 'Delete') AND (OLD.status NOT IN ('Deleted', 'Delete') OR OLD.status IS NULL)) THEN
        
        -- 1. Cascade to vendor_purchase_invoice_item_info
        UPDATE vendor_purchase_invoice_item_info
        SET status = NEW.status,
            updated_by = COALESCE(NEW.updated_by, updated_by),
            updated_date = COALESCE(NEW.updated_date, NOW())
        WHERE vendor_po_item_id = NEW.vendor_po_item_id
          AND (status NOT IN ('Deleted', 'Delete') OR status IS NULL);

        -- 2. Cascade to vendor_purchase_multiple_invoice_item_info
        UPDATE vendor_purchase_multiple_invoice_item_info
        SET status = NEW.status,
            updated_by = COALESCE(NEW.updated_by, updated_by),
            updated_date = COALESCE(NEW.updated_date, NOW())
        WHERE vendor_po_item_id = NEW.vendor_po_item_id
          AND (status NOT IN ('Deleted', 'Delete') OR status IS NULL);

        -- 3. Cascade to vendor_pur_inward_item_info
        UPDATE vendor_pur_inward_item_info
        SET status = NEW.status,
            updated_by = COALESCE(NEW.updated_by, updated_by),
            updated_date = COALESCE(NEW.updated_date, NOW())
        WHERE vendor_po_item_id = NEW.vendor_po_item_id
          AND (status NOT IN ('Deleted', 'Delete') OR status IS NULL);

    END IF;
END$$

DELIMITER ;

-- =====================================================================================
-- Historical Data Synchronization (One-Time Run)
-- =====================================================================================

-- 1. Sync vendor_purchase_invoice_item_info
UPDATE vendor_purchase_invoice_item_info inv_item
JOIN vendor_po_item_info po_item ON inv_item.vendor_po_item_id = po_item.vendor_po_item_id
SET inv_item.status = po_item.status,
    inv_item.updated_date = NOW()
WHERE po_item.status IN ('Delete', 'Deleted')
  AND inv_item.status = 'Active';

-- 2. Sync vendor_purchase_multiple_invoice_item_info
UPDATE vendor_purchase_multiple_invoice_item_info minv_item
JOIN vendor_po_item_info po_item ON minv_item.vendor_po_item_id = po_item.vendor_po_item_id
SET minv_item.status = po_item.status,
    minv_item.updated_date = NOW()
WHERE po_item.status IN ('Delete', 'Deleted')
  AND minv_item.status = 'Active';

-- 3. Sync vendor_pur_inward_item_info
UPDATE vendor_pur_inward_item_info inw_item
JOIN vendor_po_item_info po_item ON inw_item.vendor_po_item_id = po_item.vendor_po_item_id
SET inw_item.status = po_item.status,
    inw_item.updated_date = NOW()
WHERE po_item.status IN ('Delete', 'Deleted')
  AND inw_item.status = 'Active';

